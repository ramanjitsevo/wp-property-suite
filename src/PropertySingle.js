import React, { useState, useEffect, useRef } from 'react';
import './PropertySingle.css';

const STATUS_LABELS = {
  'for-sale': 'For Sale',
  'for-rent': 'For Rent',
  'sold': 'Sold',
  'rented': 'Rented',
};

const FEATURE_EXCLUDE_FIELDS = [
  'id',
  'title',
  'content',
  'excerpt',
  'date',
  'thumbnail',
  'thumbnail_url',
  'price',
  'area',
  'address',
  'city',
  'state',
  'zipcode',
  'country',
  'status',
  'property_type',
  'location',
  'bedrooms',
  'bathrooms',
  'floor',
  'gallery',
  'gallery_urls',
  'garage',
  'agent',
  'agent_name',
  'agent_phone',
  'agent_email',
  'agent_photo',
  'agentName',
  'agentPhone',
  'agentEmail',
  'agentPhoto',
  'additional',
  'additional_details',
  'additionalDetails',
  'faq',
  'faqs',
  'property_faqs',
  'propertyFaqs',
];

const normalizeArrayField = (rawValue) => {
  if (Array.isArray(rawValue)) return rawValue;
  if (typeof rawValue === 'string' && rawValue.trim()) {
    try {
      const parsed = JSON.parse(rawValue);
      return Array.isArray(parsed) ? parsed : [parsed];
    } catch (e) {
      return [rawValue];
    }
  }
  if (rawValue && typeof rawValue === 'object') return [rawValue];
  return [];
};

function PropertySingle({ property, onBack, settings }) {
  const [activeTab, setActiveTab] = useState('overview');
  const [mainImage, setMainImage] = useState(0);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const [isFavorite, setIsFavorite] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState(false);
  const [submitError, setSubmitError] = useState('');
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    message: "I'm interested in this property..."
  });

  const contactFormRef = useRef(null);
  const statusLabel = STATUS_LABELS[property?.status] || property?.status || 'For Sale';

  const galleryImages = (() => {
    if (property?.gallery && property.gallery.length > 0) {
      const thumb = property.thumbnail;
      return thumb && !property.gallery.includes(thumb)
        ? [thumb, ...property.gallery]
        : property.gallery;
    }
    return [property?.thumbnail || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800'];
  })();

  const additionalDetails = normalizeArrayField(
    property?.additional_details || property?.additionalDetails || property?.additional || null
  );

  const customFaqItems = normalizeArrayField(property?.faqs || property?.faq || property?.property_faqs || property?.propertyFaqs)
    .map((item, index) => {
      if (item === null || item === undefined) return null;
      if (typeof item === 'object') {
        const question = item.question || item.title || item.label || `Question ${index + 1}`;
        const answer = item.answer || item.value || item.text || item.description || '';
        return question && answer ? { question, answer } : null;
      }
      return { question: `Question ${index + 1}`, answer: String(item) };
    })
    .filter(Boolean);

  const fallbackFaqItems = [
    {
      question: `What is the price of ${property?.title || 'this property'}?`,
      answer: property?.price ? `The listed price is ${property.price}.` : 'Please request information for the latest property price.',
    },
    {
      question: 'Where is this property located?',
      answer: [property?.address, property?.city, property?.state, property?.zipcode, property?.country].filter(Boolean).join(', ') || 'The exact location details are available on request.',
    },
    {
      question: 'How can I schedule a property visit?',
      answer: 'Use the Schedule a Tour button or submit the inquiry form on this page to request a visit.',
    },
    {
      question: 'What are the key property features?',
      answer: [
        property?.bedrooms ? `${property.bedrooms} bedroom(s)` : '',
        property?.bathrooms ? `${property.bathrooms} bathroom(s)` : '',
        property?.area ? `${property.area} sq ft` : '',
        property?.garage ? `${property.garage} garage space(s)` : '',
      ].filter(Boolean).join(', ') || 'Key features are listed in the Features tab.',
    },
  ];

  const faqItems = customFaqItems.length ? customFaqItems : fallbackFaqItems;

  const tabs = [
    { id: 'overview', label: 'Overview' },
    { id: 'features', label: 'Features' },
    { id: 'location', label: 'Location' },
    { id: 'additional', label: 'Additional Details' },
    { id: 'faq', label: 'FAQ' },
  ];

  useEffect(() => {
    if (property?.id) {
      const favs = JSON.parse(localStorage.getItem('property_favorites') || '[]');
      setIsFavorite(favs.includes(property.id));
    }
  }, [property?.id]);

  const handleInputChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const scrollToContact = () => {
    contactFormRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  const handleScheduleTour = () => {
    if (settings?.scheduleTourUrl) {
      window.open(settings.scheduleTourUrl, '_blank');
    } else {
      scrollToContact();
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setSubmitError('');
    setSubmitSuccess(false);

    const payload = {
      name: formData.name,
      email: formData.email,
      phone: formData.phone,
      message: formData.message,
      propertyId: property?.id || 0,
      propertyTitle: property?.title || '',
    };

    try {
      const data = await fetch(`${propertyPluginData.apiUrl}/leads`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': propertyPluginData.nonce,
        },
        body: JSON.stringify(payload),
      });
      const result = await data.json();
      if (!data.ok) throw new Error(result.message || `Server error (${data.status})`);
      setSubmitSuccess(true);
      setFormData({ name: '', email: '', phone: '', message: "I'm interested in this property..." });
    } catch (err) {
      setSubmitError(err.message || 'Something went wrong. Please try again.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const toggleFavorite = () => {
    const favs = JSON.parse(localStorage.getItem('property_favorites') || '[]');
    let updated;
    if (isFavorite) {
      updated = favs.filter(id => id !== property.id);
    } else {
      updated = [...favs, property.id];
    }
    localStorage.setItem('property_favorites', JSON.stringify(updated));
    setIsFavorite(!isFavorite);
  };

  const handleShare = async () => {
    const url = window.location.href;
    const title = property?.title || 'Check out this property';
    if (navigator.share) {
      try { await navigator.share({ title, url }); } catch (_) {}
    } else {
      await navigator.clipboard.writeText(url);
      alert('Property link copied to clipboard!');
    }
  };

  const getPageUrl = () => encodeURIComponent(window.location.href);
  const getPageTitle = () => encodeURIComponent(property?.title || 'Check out this property');

  const renderBottomBar = () => (
    <div className="property-bottom-bar property-bottom-bar-spaced">
      <div className="bottom-price">
        <span className="price-amount">{property.price}</span>
        <span className="price-badge">{statusLabel}</span>
      </div>
      <div className="bottom-actions">
        <a href={`tel:${(settings?.agentPhone || '').replace(/[^+\d]/g, '')}`} className="btn-call">
          <i className="fas fa-phone"></i> {settings?.agentPhone || 'Call Agent'}
        </a>
        <button className="btn-schedule" onClick={handleScheduleTour}>
          <i className="far fa-calendar-check"></i> Schedule a Tour
        </button>
        <button className="btn-request-info" onClick={scrollToContact}>
          Request Information
        </button>
      </div>
    </div>
  );

  if (!property) {
    return <div className="property-plugin-loading">Loading property details...</div>;
  }

  return (
    <article className="property-single-page" itemScope itemType="https://schema.org/RealEstateListing">
      {faqItems.length > 0 && (
        <script type="application/ld+json">
          {JSON.stringify({
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            mainEntity: faqItems.map((item) => ({
              '@type': 'Question',
              name: item.question,
              acceptedAnswer: {
                '@type': 'Answer',
                text: item.answer,
              },
            })),
          })}
        </script>
      )}

      <nav className="property-breadcrumb" aria-label="Breadcrumb">
        <div className="property-plugin-container">
          <button type="button" onClick={onBack} className="breadcrumb-link">Home</button>
          <span className="breadcrumb-separator">›</span>
          <button type="button" onClick={onBack} className="breadcrumb-link">Properties</button>
          <span className="breadcrumb-separator">›</span>
          <span className="breadcrumb-current">{property.title}</span>
        </div>
      </nav>

      <div className="property-plugin-container">
        <div className="property-single-layout">
          <div className="property-single-main">
            <div className="property-gallery">
              <div className="gallery-main">
                <img
                  src={galleryImages[mainImage]}
                  alt={property.title}
                  className={`gallery-main-image ${isFullscreen ? 'gallery-main-image-fullscreen' : ''}`}
                  itemProp="image"
                  onClick={() => isFullscreen && setIsFullscreen(false)}
                />
                {!isFullscreen && (
                  <>
                    <div className="gallery-badge">{statusLabel}</div>
                    <div className="gallery-actions">
                      <button className="gallery-action-btn" title="Add to favorites" onClick={toggleFavorite}>
                        <i className={`${isFavorite ? 'fas' : 'far'} fa-heart ${isFavorite ? 'favorite-icon-active' : ''}`}></i>
                      </button>
                      <button className="gallery-action-btn" title="Fullscreen" onClick={() => setIsFullscreen(true)}>
                        <i className="fas fa-expand-arrows-alt"></i>
                      </button>
                    </div>
                    <div className="gallery-counter">{mainImage + 1} / {galleryImages.length}</div>
                  </>
                )}
                {isFullscreen && (
                  <div className="gallery-fullscreen-controls">
                    <button className="gallery-fullscreen-btn" onClick={() => setMainImage(Math.max(0, mainImage - 1))}>
                      <i className="fas fa-chevron-left"></i>
                    </button>
                    <span className="gallery-fullscreen-count">{mainImage + 1} / {galleryImages.length}</span>
                    <button className="gallery-fullscreen-btn" onClick={() => setMainImage(Math.min(galleryImages.length - 1, mainImage + 1))}>
                      <i className="fas fa-chevron-right"></i>
                    </button>
                    <button className="gallery-fullscreen-btn" onClick={() => setIsFullscreen(false)}>
                      <i className="fas fa-times"></i>
                    </button>
                  </div>
                )}
              </div>
              {!isFullscreen && (
                <div className="gallery-thumbnails">
                  <button
                    className="gallery-nav gallery-nav-prev"
                    onClick={() => setMainImage(Math.max(0, mainImage - 1))}
                  >
                    <i className="fas fa-chevron-left"></i>
                  </button>
                  {galleryImages.map((img, index) => (
                    <div
                      key={index}
                      className={`gallery-thumb ${index === mainImage ? 'active' : ''}`}
                      onClick={() => setMainImage(index)}
                    >
                      <img src={img} alt={`View ${index + 1}`} />
                    </div>
                  ))}
                  <button
                    className="gallery-nav gallery-nav-next"
                    onClick={() => setMainImage(Math.min(galleryImages.length - 1, mainImage + 1))}
                  >
                    <i className="fas fa-chevron-right"></i>
                  </button>
                </div>
              )}
            </div>

            <nav className="property-tabs" aria-label="Property details">
              {tabs.map((tab) => (
                <button
                  key={tab.id}
                  className={`property-tab ${activeTab === tab.id ? 'active' : ''}`}
                  onClick={() => setActiveTab(tab.id)}
                  aria-pressed={activeTab === tab.id}
                >
                  {tab.label}
                </button>
              ))}
            </nav>

            {activeTab === 'overview' && (
              <section className="property-overview-section" aria-labelledby="property-overview-heading">
                <div className="overview-two-column">
                  <div className="overview-description">
                    <h2 id="property-overview-heading">About This Property</h2>
                    {property.content ? (
                      <div itemProp="description" dangerouslySetInnerHTML={{ __html: property.content }} />
                    ) : (
                      <p><em>No description available for this property.</em></p>
                    )}
                  </div>
                  <div className="overview-map">
                    <h2>Property Location</h2>
                    <div className="map-placeholder">
                      {property.address ? (
                        <address className="map-address" itemProp="address">
                          <div className="map-pin"><i className="fas fa-map-marker-alt"></i></div>
                          <p className="map-address-line">{property.address}</p>
                          {[property.city, property.state, property.zipcode].filter(Boolean).join(', ')}
                        </address>
                      ) : (
                        <>
                          <div className="map-pin"><i className="fas fa-map-marker-alt"></i></div>
                          <p>Address not available</p>
                        </>
                      )}
                    </div>
                  </div>
                </div>
                {renderBottomBar()}
              </section>
            )}

            {activeTab === 'features' && (
              <section className="property-overview-section" aria-labelledby="property-features-heading">
                <h2 id="property-features-heading" className="section-heading">Property Features & Details</h2>
                <dl className="features-grid">
                  {[
                    { icon: 'fa-bed', label: 'Bedrooms', value: property.bedrooms || '0' },
                    { icon: 'fa-bath', label: 'Bathrooms', value: property.bathrooms || '0' },
                    { icon: 'fa-car', label: 'Garage', value: property.garage ? `${property.garage} car(s)` : 'No garage' },
                    { icon: 'fa-vector-square', label: 'Area', value: property.area ? `${property.area} sq ft` : 'N/A' },
                    { icon: 'fa-building', label: 'Property Type', value: property.property_type || 'N/A' },
                    { icon: 'fa-layer-group', label: 'Floor', value: property.floor || 'N/A' },
                    { icon: 'fa-map-marker-alt', label: 'City', value: property.city || 'N/A' },
                    { icon: 'fa-globe', label: 'Country', value: property.country || 'N/A' },
                  ].filter(f => f.value !== 'N/A' && f.value !== '0' || ['Bedrooms', 'Bathrooms'].includes(f.label)).map((f, i) => (
                    <div key={i} className="feature-detail-card">
                      <i className={`fas ${f.icon} feature-detail-icon`}></i>
                      <div>
                        <dt className="feature-detail-label">{f.label}</dt>
                        <dd className="feature-detail-value">{f.value}</dd>
                      </div>
                    </div>
                  ))}
                  {Object.keys(property)
                    .filter(key => !FEATURE_EXCLUDE_FIELDS.includes(key) && property[key] && property[key] !== 'N/A' && property[key] !== '')
                    .map((slug) => {
                      const label = slug.replace(/property-/g, '').replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                      const rawValue = property[slug];
                      const displayValue = typeof rawValue === 'object'
                        ? rawValue.name || rawValue.title || JSON.stringify(rawValue)
                        : rawValue;

                      return (
                        <div key={slug} className="feature-detail-card">
                          <i className="fas fa-tag feature-detail-icon"></i>
                          <div>
                            <dt className="feature-detail-label">{label}</dt>
                            <dd className="feature-detail-value">{displayValue}</dd>
                          </div>
                        </div>
                      );
                    })}
                </dl>
                {renderBottomBar()}
              </section>
            )}

            {activeTab === 'location' && (
              <section className="property-overview-section" aria-labelledby="property-location-heading">
                <h2 id="property-location-heading" className="section-heading">Property Location</h2>
                <div className="map-placeholder map-placeholder-compact">
                  {property.address ? (
                    <address className="map-address" itemProp="address">
                      <div className="map-pin map-pin-compact"><i className="fas fa-map-marker-alt"></i></div>
                      <p className="map-address-line map-address-line-large">{property.address}</p>
                      <p className="map-address-region">{[property.city, property.state, property.zipcode, property.country].filter(Boolean).join(', ')}</p>
                    </address>
                  ) : (
                    <>
                      <div className="map-pin"><i className="fas fa-map-marker-alt"></i></div>
                      <p>Address not available</p>
                    </>
                  )}
                </div>

                <dl className="location-detail-list">
                  {[
                    { label: 'Address', value: property.address },
                    { label: 'City', value: property.city },
                    { label: 'State', value: property.state },
                    { label: 'Zip Code', value: property.zipcode },
                    { label: 'Country', value: property.country },
                  ].filter(row => row.value).map(row => (
                    <div key={row.label} className="info-row location-info-row">
                      <dt className="info-label">{row.label}</dt>
                      <dd className="info-value">{row.value}</dd>
                    </div>
                  ))}
                </dl>
                {renderBottomBar()}
              </section>
            )}

            {activeTab === 'additional' && (
              <section className="property-overview-section" aria-labelledby="property-additional-heading">
                <h2 id="property-additional-heading" className="section-heading">Additional Details</h2>
                {additionalDetails.length === 0 ? (
                  <p><em>No additional details available for this property.</em></p>
                ) : (
                  <dl className="additional-details-grid">
                    {additionalDetails.map((item, idx) => {
                      if (item === null || item === undefined) return null;
                      if (typeof item === 'object') {
                        const label = item.label || item.name || item.title || item.key || `Detail ${idx + 1}`;
                        const value = item.value || item.text || item.phone || item.email || item.description || JSON.stringify(item);
                        return (
                          <div key={idx} className="additional-detail-card">
                            <dt className="feature-detail-label">{label}</dt>
                            <dd className="feature-detail-value additional-detail-value">{value}</dd>
                          </div>
                        );
                      }
                      return (
                        <div key={idx} className="additional-detail-card">
                          <dt className="feature-detail-label">{`Detail ${idx + 1}`}</dt>
                          <dd className="feature-detail-value additional-detail-value">{String(item)}</dd>
                        </div>
                      );
                    })}
                  </dl>
                )}
                {renderBottomBar()}
              </section>
            )}

            {activeTab === 'faq' && (
              <section className="property-overview-section" aria-labelledby="property-faq-heading">
                <h2 id="property-faq-heading" className="section-heading">Frequently Asked Questions</h2>
                <div className="property-faq-list">
                  {faqItems.map((item, idx) => (
                    <details className="property-faq-item" key={idx}>
                      <summary>{item.question}</summary>
                      <p>{item.answer}</p>
                    </details>
                  ))}
                </div>
                {renderBottomBar()}
              </section>
            )}
          </div>

          <div className="property-single-sidebar" role="complementary" aria-label="Property summary and inquiry">
            <div className="property-details-card">
              <div className="details-header">
                <span className="featured-label">{settings?.featuredLabel || 'FEATURED PROPERTY'}</span>
                <h1 className="property-title" itemProp="name">{property.title}</h1>
                <p className="property-address">
                  <i className="fas fa-map-marker-alt"></i> {property.address}
                </p>
                <div className="property-price-large">
                  <span className="price">{property.price}</span>
                  <span className="status-badge">{statusLabel}</span>
                </div>
              </div>

              <div className="property-quick-stats">
                <div className="stat-item">
                  <span className="stat-icon"><i className="fas fa-bed"></i></span>
                  <div>
                    <span className="stat-value">{property.bedrooms || '0'}</span>
                    <span className="stat-label">Bedrooms</span>
                  </div>
                </div>
                <div className="stat-item">
                  <span className="stat-icon"><i className="fas fa-bath"></i></span>
                  <div>
                    <span className="stat-value">{property.bathrooms || '0'}</span>
                    <span className="stat-label">Bathrooms</span>
                  </div>
                </div>
                <div className="stat-item">
                  <div>
                    <span className="stat-value">{property.area || '0'}</span>
                    <span className="stat-label">Sq Ft</span>
                  </div>
                </div>
                <div className="stat-item">
                  <span className="stat-icon"><i className="fas fa-car"></i></span>
                  <div>
                    <span className="stat-value">{property.garage || '0'}</span>
                    <span className="stat-label">Garage</span>
                  </div>
                </div>
              </div>

              <div className="property-info-list">
                <h4 className="property-title">Location Detail</h4>
                <div className="info-row">
                  <span className="info-label"><i className="fas fa-building"></i> Property Type:</span>
                  <span className="info-value">{property.property_type || 'N/A'}</span>
                </div>
                {property.city && (
                  <div className="info-row">
                    <span className="info-label"><i className="fas fa-city"></i> City:</span>
                    <span className="info-value">{property.city}</span>
                  </div>
                )}
                {property.state && (
                  <div className="info-row">
                    <span className="info-label"><i className="fas fa-map-marked-alt"></i> State:</span>
                    <span className="info-value">{property.state}</span>
                  </div>
                )}
                {property.zipcode && (
                  <div className="info-row">
                    <span className="info-label"><i className="fas fa-mail-bulk"></i> Zip Code:</span>
                    <span className="info-value">{property.zipcode}</span>
                  </div>
                )}
                {property.country && (
                  <div className="info-row">
                    <span className="info-label"><i className="fas fa-globe"></i> Country:</span>
                    <span className="info-value">{property.country}</span>
                  </div>
                )}
              </div>
            </div>

            <div className="contact-form-card" ref={contactFormRef}>
              <h3>{settings?.contactFormHeading || 'Get More Details'}</h3>
              <p className="form-subtitle">{settings?.contactFormSubtitle || 'Schedule a tour or request more information about this property.'}</p>

              {submitSuccess && (
                <div className="form-alert form-alert-success" role="status">
                  ✓ Thank you! Your inquiry has been sent. We'll be in touch shortly.
                </div>
              )}

              <form onSubmit={handleSubmit}>
                <div className="form-group">
                  <input
                    type="text"
                    name="name"
                    placeholder="Your Name*"
                    value={formData.name}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="form-group">
                  <input
                    type="email"
                    name="email"
                    placeholder="Your Email*"
                    value={formData.email}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="form-group">
                  <input
                    type="tel"
                    name="phone"
                    placeholder="Your Phone Number*"
                    value={formData.phone}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="form-group">
                  <textarea
                    name="message"
                    placeholder="I'm interested in this property..."
                    value={formData.message}
                    onChange={handleInputChange}
                    rows="4"
                  ></textarea>
                </div>

                {submitError && (
                  <div className="form-alert form-alert-error" role="alert">
                    ⚠ {submitError}
                  </div>
                )}

                <button type="submit" className="btn-submit-form" disabled={isSubmitting}>
                  {isSubmitting ? 'Sending...' : 'Request Information'}
                </button>
                <p className="form-privacy">
                  <i className="fas fa-lock"></i> Your information is safe with us. We don't share your details with third parties.
                </p>
              </form>
            </div>

            <div className="property-actions">
              <button className="action-btn" onClick={toggleFavorite}>
                <span className="action-icon">
                  <i className={`${isFavorite ? 'fas' : 'far'} fa-heart ${isFavorite ? 'favorite-icon-active' : ''}`}></i>
                </span>
                {isFavorite ? 'Saved to Favorites' : 'Add to Favorites'}
              </button>
              <button className="action-btn" onClick={handleShare}>
                <span className="action-icon"><i className="fas fa-share-alt"></i></span>
                Share Property
              </button>
              <div className="social-share">
                <a href={`https://www.facebook.com/sharer/sharer.php?u=${getPageUrl()}`} target="_blank" rel="noopener noreferrer" className="social-icon" title="Share on Facebook"><i className="fab fa-facebook-f"></i></a>
                <a href={`https://twitter.com/intent/tweet?url=${getPageUrl()}&text=${getPageTitle()}`} target="_blank" rel="noopener noreferrer" className="social-icon" title="Share on Twitter"><i className="fab fa-twitter"></i></a>
                <a href={`https://www.linkedin.com/shareArticle?mini=true&url=${getPageUrl()}&title=${getPageTitle()}`} target="_blank" rel="noopener noreferrer" className="social-icon" title="Share on LinkedIn"><i className="fab fa-linkedin-in"></i></a>
                <a href={`mailto:?subject=${getPageTitle()}&body=${getPageUrl()}`} className="social-icon" title="Share via Email"><i className="fas fa-envelope"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </article>
  );
}

export default PropertySingle;
