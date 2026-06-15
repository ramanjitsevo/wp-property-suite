import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
  console.log('WP Property Suite - DOM loaded, searching for containers...');
  
  // Find all property plugin containers
  const containers = document.querySelectorAll('.wps-container');
  
  console.log('Found containers:', containers.length);
  
  if (containers.length === 0) {
    // Create a container automatically as fallback
    console.log('No container found, creating one...');
    const newContainer = document.createElement('div');
    newContainer.id = 'wps-root-auto';
    newContainer.className = 'wps-container';
    document.body.appendChild(newContainer);
    
    const root = ReactDOM.createRoot(newContainer);
    root.render(
      <React.StrictMode>
        <App containerId={newContainer.id} />
      </React.StrictMode>
    );
  } else {
    containers.forEach((container) => {
      console.log('Mounting React to container:', container.id);
      const root = ReactDOM.createRoot(container);
      root.render(
        <React.StrictMode>
          <App containerId={container.id} />
        </React.StrictMode>
      );
    });
  }
});
