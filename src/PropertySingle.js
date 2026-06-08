import React, { useState, useEffect, useRef } from 'react';
import './PropertySingle.css';

const STATUS_LABELS = {
  'for-sale': 'For Sale',
  'for-rent': 'For Rent',
  'sold': 'Sold',
  'rented': 'Rented',
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

  // Derive dynamic values
  const statusLabel = STATUS_LABELS[property?.status] || property?.status || 'For Sale';
  const agentPhoto = settings?.agentPhoto || 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=100';

  // Build gallery from property data
  const galleryImages = (() => {
    if (property?.gallery && property.gallery.length > 0) {
      const thumb = property.thumbnail;
      return thumb && !property.gallery.includes(thumb)
        ? [thumb, ...property.gallery]
        : property.gallery;
    }
    return [property?.thumbnail || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800'];
  })();

  // Load favorite state from localStorage
  useEffect(() => {
    if (property?.id) {
      const favs = JSON.parse(localStorage.getItem('property_favorites') || '[]');
      setIsFavorite(favs.includes(property.id));
    }
  }, [property?.id]);

  const handleInputChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // Scroll to contact form
  const scrollToContact = () => {
    contactFormRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  // Schedule tour — external URL or scroll to form
  const handleScheduleTour = () => {
    if (settings?.scheduleTourUrl) {
      window.open(settings.scheduleTourUrl, '_blank');
    } else {
      scrollToContact();
    }
  };

  // Contact form submit via REST API
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

  // Toggle favorite
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

  // Share property
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

  // Social share URLs
  const getPageUrl = () => encodeURIComponent(window.location.href);
  const getPageTitle = () => encodeURIComponent(property?.title || 'Check out this property');

  if (!property) {
    return <div className="property-plugin-loading">Loading property details...</div>;
  }

  return (
    <div className="property-single-page">
      {/* Breadcrumb */}
      <div className="property-breadcrumb">
        <div className="property-plugin-container">
          <span onClick={onBack} className="breadcrumb-link">Home</span>
          <span className="breadcrumb-separator">›</span>
          <span onClick={onBack} className="breadcrumb-link">Properties</span>
          <span className="breadcrumb-separator">›</span>
          <span className="breadcrumb-current">{property.title}</span>
        </div>
      </div>

      {/* Main Content Layout */}
      <div className="property-plugin-container">
        <div className="property-single-layout">

          {/* Left Column - Gallery & Details */}
          <div className="property-single-main">

            {/* Image Gallery */}
            <div className="property-gallery">
              <div className="gallery-main">
                <img
                  src={isFullscreen ? galleryImages[mainImage] : galleryImages[mainImage]}
                  alt={property.title}
                  className="gallery-main-image"
                  style={isFullscreen ? {
                    position: 'fixed', top: 0, left: 0, width: '100vw', height: '100vh',
                    objectFit: 'contain', zIndex: 9999, background: '#000', cursor: 'zoom-out'
                  } : {}}
                  onClick={() => isFullscreen && setIsFullscreen(false)}
                />
                {!isFullscreen && (
                  <>
                    <div className="gallery-badge">{statusLabel}</div>
                    <div className="gallery-actions">
                      <button className="gallery-action-btn" title="Add to favorites" onClick={toggleFavorite}>
                        <i className={isFavorite ? 'fas fa-heart' : 'far fa-heart'} style={isFavorite ? { color: '#e53e3e' } : {}}></i>
                      </button>
                      <button className="gallery-action-btn" title="Fullscreen" onClick={() => setIsFullscreen(true)}>
                        <i className="fas fa-expand-arrows-alt"></i>
                      </button>
                    </div>
                    <div className="gallery-counter">{mainImage + 1} / {galleryImages.length}</div>
                  </>
                )}
                {isFullscreen && (
                  <div style={{ position: 'fixed', bottom: 30, left: '50%', transform: 'translateX(-50%)', zIndex: 10000, display: 'flex', gap: 10, alignItems: 'center' }}>
                    <button onClick={() => setMainImage(Math.max(0, mainImage - 1))} style={{ background: 'rgba(255,255,255,0.2)', color: '#fff', border: 'none', padding: '8px 14px', borderRadius: 4, cursor: 'pointer' }}>
                      <i className="fas fa-chevron-left"></i>
                    </button>
                    <span style={{ color: '#fff' }}>{mainImage + 1} / {galleryImages.length}</span>
                    <button onClick={() => setMainImage(Math.min(galleryImages.length - 1, mainImage + 1))} style={{ background: 'rgba(255,255,255,0.2)', color: '#fff', border: 'none', padding: '8px 14px', borderRadius: 4, cursor: 'pointer' }}>
                      <i className="fas fa-chevron-right"></i>
                    </button>
                    <button onClick={() => setIsFullscreen(false)} style={{ background: 'rgba(255,255,255,0.2)', color: '#fff', border: 'none', padding: '8px 14px', borderRadius: 4, cursor: 'pointer' }}>
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

            {/* Tabs Navigation */}
            <div className="property-tabs">
              {['Overview', 'Features', 'Location'].map((tab) => (
                <button
                  key={tab}
                  className={`property-tab ${activeTab === tab.toLowerCase() ? 'active' : ''}`}
                  onClick={() => setActiveTab(tab.toLowerCase())}
                >
                  {tab}
                </button>
              ))}
            </div>

            {/* === OVERVIEW TAB === */}
            {activeTab === 'overview' && (
              <div className="property-overview-section">
                <div className="overview-two-column">
                  <div className="overview-description">
                    <h3>About This Property</h3>
                    {property.content ? (
                      <div dangerouslySetInnerHTML={{ __html: property.content }} />
                    ) : (
                      <p><em>No description available for this property.</em></p>
                    )}
                  </div>
                  <div className="overview-map">
                    <h3>Property Location</h3>
                    <div className="map-placeholder">
                      {property.address ? (
                        <div style={{ textAlign: 'center' }}>
                          <div className="map-pin"><i className="fas fa-map-marker-alt"></i></div>
                          <p style={{ margin: '8px 0 4px', fontWeight: 600 }}>{property.address}</p>
                          {[property.city, property.state, property.zipcode].filter(Boolean).join(', ')}
                        </div>
                      ) : (
                        <>
                          <div className="map-pin"><i className="fas fa-map-marker-alt"></i></div>
                          <p>Address not available</p>
                        </>
                      )}
                    </div>
                  </div>
                </div>

                {/* Bottom Price Bar */}
                <div className="property-bottom-bar">
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
              </div>
            )}

            {/* === FEATURES TAB === */}
            {activeTab === 'features' && (
              <div className="property-overview-section">
                <h3 style={{ marginBottom: 20 }}>Property Features & Details</h3>
                <div className="features-grid" style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(220px, 1fr))', gap: 16 }}>
                  {[
                    { icon: 'fa-bed',        label: 'Bedrooms',    value: property.bedrooms || '0' },
                    { icon: 'fa-bath',       label: 'Bathrooms',   value: property.bathrooms || '0' },
                    { icon: 'fa-car',        label: 'Garage',      value: property.garage ? `${property.garage} car(s)` : 'No garage' },
                    { icon: 'fa-vector-square', label: 'Area',     value: property.area ? `${property.area} sq ft` : 'N/A' },
                    { icon: 'fa-building',   label: 'Property Type', value: property.property_type || 'N/A' },
                    { icon: 'fa-layer-group', label: 'Floor',      value: property.floor || 'N/A' },
                    { icon: 'fa-map-marker-alt', label: 'City',    value: property.city || 'N/A' },
                    { icon: 'fa-globe',      label: 'Country',     value: property.country || 'N/A' },
                  ].filter(f => f.value !== 'N/A' && f.value !== '0' || ['Bedrooms', 'Bathrooms'].includes(f.label)).map((f, i) => (
                    <div key={i} style={{ background: '#f8f9fa', borderRadius: 10, padding: '18px', display: 'flex', alignItems: 'center', gap: 14 }}>
                      <i className={`fas ${f.icon}`} style={{ fontSize: 22, color: settings?.primaryColor || '#2196f3' }}></i>
                      <div>
                        <div style={{ fontSize: 12, color: '#888' }}>{f.label}</div>
                        <div style={{ fontWeight: 600, color: '#1a1a2e' }}>{f.value}</div>
                      </div>
                    </div>
                  ))}

                  {/* Auto-discovered custom taxonomies */}
                  {(() => {
                    const excludeFields = ['id','title','content','excerpt','date','thumbnail','price','area','address','city','state','zipcode','country','status','property_type','location','bedrooms','bathrooms','floor','gallery','garage'];
                    const customs = Object.keys(property).filter(k =>
                      !excludeFields.includes(k) && property[k] && property[k] !== 'N/A' && property[k] !== ''
                    );
                    return customs.map(slug => {
                      const label = slug.replace(/property-/g, '').replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                      return (
                        <div key={slug} style={{ background: '#f8f9fa', borderRadius: 10, padding: '18px', display: 'flex', alignItems: 'center', gap: 14 }}>
                          <i className="fas fa-tag" style={{ fontSize: 22, color: settings?.primaryColor || '#2196f3' }}></i>
                          <div>
                            <div style={{ fontSize: 12, color: '#888' }}>{label}</div>
                            <div style={{ fontWeight: 600, color: '#1a1a2e' }}>{property[slug]}</div>
                          </div>
                        </div>
                      );
                    });
                  })()}
                </div>

                {/* Bottom Price Bar */}
                <div className="property-bottom-bar" style={{ marginTop: 30 }}>
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
              </div>
            )}

            {/* === LOCATION TAB === */}
            {activeTab === 'location' && (
              <div className="property-overview-section">
                <h3 style={{ marginBottom: 20 }}>Property Location</h3>
                <div className="map-placeholder" style={{ maxWidth: '100%', minHeight: 200 }}>
                  {property.address ? (
                    <div style={{ textAlign: 'center' }}>
                      <div className="map-pin"><i className="fas fa-map-marker-alt" style={{ fontSize: 36 }}></i></div>
                      <p style={{ margin: '12px 0 4px', fontWeight: 700, fontSize: 16 }}>{property.address}</p>
                      <p style={{ color: '#555' }}>{[property.city, property.state, property.zipcode, property.country].filter(Boolean).join(', ')}</p>
                    </div>
                  ) : (
                    <>
                      <div className="map-pin"><i className="fas fa-map-marker-alt"></i></div>
                      <p>Address not available</p>
                    </>
                  )}
                </div>

                {/* Location details list */}
                <div style={{ marginTop: 24, display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px 30px' }}>
                  {[
                    { label: 'Address', value: property.address },
                    { label: 'City', value: property.city },
                    { label: 'State', value: property.state },
                    { label: 'Zip Code', value: property.zipcode },
                    { label: 'Country', value: property.country },
                  ].filter(r => r.value).map(r => (
                    <div key={r.label} className="info-row" style={{ display: 'flex', justifyContent: 'space-between', borderBottom: '1px solid #f0f0f0', paddingBottom: 8 }}>
                      <span className="info-label">{r.label}</span>
                      <span className="info-value">{r.value}</span>
                    </div>
                  ))}
                </div>

                {/* Bottom Price Bar */}
                <div className="property-bottom-bar" style={{ marginTop: 30 }}>
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
              </div>
            )}
          </div>

          {/* Right Column - Sidebar */}
          <div className="property-single-sidebar">

            {/* Property Details */}
            <div className="property-details-card">
              <div className="details-header">
                <span className="featured-label">{settings?.featuredLabel || 'FEATURED PROPERTY'}</span>
                <h1 className="property-title">{property.title}</h1>
                <p className="property-address"><i className="fas fa-map-marker-alt"></i> {property.address}</p>
                <div className="property-price-large">
                  <span className="price">{property.price}</span>
                  <span className="status-badge">{statusLabel}</span>
                </div>
              </div>

              {/* Quick Stats */}
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

              {/* Detailed Information */}
              <div className="property-info-list">
                <div className="info-row">
                  <span className="info-label">Property Type:</span>
                  <span className="info-value">{property.property_type || 'N/A'}</span>
                </div>
                <div className="info-row">
                  <span className="info-label">Property Status:</span>
                  <span className="info-value">{statusLabel}</span>
                </div>
                <div className="info-row">
                  <span className="info-label">Property ID:</span>
                  <span className="info-value">{property.id}</span>
                </div>

                {property.city && (
                  <div className="info-row">
                    <span className="info-label">City:</span>
                    <span className="info-value">{property.city}</span>
                  </div>
                )}
                {property.state && (
                  <div className="info-row">
                    <span className="info-label">State:</span>
                    <span className="info-value">{property.state}</span>
                  </div>
                )}
                {property.zipcode && (
                  <div className="info-row">
                    <span className="info-label">Zip Code:</span>
                    <span className="info-value">{property.zipcode}</span>
                  </div>
                )}
                {property.country && (
                  <div className="info-row">
                    <span className="info-label">Country:</span>
                    <span className="info-value">{property.country}</span>
                  </div>
                )}

                {/* Custom Taxonomies - Auto Display */}
                {(() => {
                  const excludeFields = ['id', 'title', 'content', 'excerpt', 'date', 'thumbnail', 'price', 'area', 'address', 'city', 'state', 'zipcode', 'country', 'status', 'property_type', 'location', 'bedrooms', 'bathrooms', 'floor', 'gallery', 'garage'];
                  const customTaxonomies = Object.keys(property).filter(key =>
                    !excludeFields.includes(key) &&
                    property[key] &&
                    property[key] !== 'N/A' &&
                    property[key] !== ''
                  );
                  if (customTaxonomies.length === 0) return null;
                  return customTaxonomies.map(taxSlug => {
                    const displayName = taxSlug
                      .replace(/property-/g, '')
                      .replace(/-/g, ' ')
                      .replace(/\b\w/g, l => l.toUpperCase());
                    return (
                      <div key={taxSlug} className="info-row">
                        <span className="info-label">{displayName}:</span>
                        <span className="info-value">{property[taxSlug]}</span>
                      </div>
                    );
                  });
                })()}
              </div>
            </div>

            {/* Contact Form */}
            <div className="contact-form-card" ref={contactFormRef}>
              <h3>{settings?.contactFormHeading || 'Get More Details'}</h3>
              <p className="form-subtitle">{settings?.contactFormSubtitle || 'Schedule a tour or request more information about this property.'}</p>

              {submitSuccess && (
                <div style={{ background: '#f0fff4', border: '1px solid #c6f6d5', borderRadius: 8, padding: '12px', marginBottom: 12, color: '#276749' }}>
                  ✓ Thank you! Your inquiry has been sent. We'll be in touch shortly.
                </div>
              )}

              <form onSubmit={handleSubmit}>
                <div className="form-group">
                  <input
                    type="text" name="name"
                    placeholder="Your Name*"
                    value={formData.name}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="form-group">
                  <input
                    type="email" name="email"
                    placeholder="Your Email*"
                    value={formData.email}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="form-group">
                  <input
                    type="tel" name="phone"
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
                  <div style={{ background: '#fff5f5', border: '1px solid #fed7d7', borderRadius: 8, padding: '10px', marginBottom: 10, color: '#c53030', fontSize: 13 }}>
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

            {/* Agent Card */}
            <div className="agent-card">
              <div className="agent-header">
                <img
                  src={agentPhoto}
                  alt={settings?.agentName || 'Agent'}
                  className="agent-photo"
                />
                <div className="agent-info">
                  <h4>{settings?.agentName || 'Agent'}</h4>
                  <p>{settings?.agentRole || 'Property Agent'}</p>
                </div>
                <div className="agent-contact">
                  <a href={`tel:${(settings?.agentPhone || '').replace(/[^+\d]/g, '')}`} className="btn-agent-phone" title="Call agent">
                    <i className="fas fa-phone"></i>
                  </a>
                  <a href={`mailto:${settings?.agentEmail || settings?.contactEmail || ''}`} className="btn-agent-email" title="Email agent">
                    <i className="fas fa-envelope"></i>
                  </a>
                </div>
              </div>
              <button className="btn-view-properties" onClick={onBack}>View All Properties</button>
            </div>

            {/* Action Buttons */}
            <div className="property-actions">
              <button className="action-btn" onClick={toggleFavorite}>
                <span className="action-icon">
                  <i className={isFavorite ? 'fas fa-heart' : 'far fa-heart'} style={isFavorite ? { color: '#e53e3e' } : {}}></i>
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
    </div>
  );
}

export default PropertySingle;
