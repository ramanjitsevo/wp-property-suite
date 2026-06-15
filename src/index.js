import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';

document.addEventListener('DOMContentLoaded', function() {
  const containers = document.querySelectorAll('.wps-container');

  if (containers.length === 0) {
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
      const root = ReactDOM.createRoot(container);
      root.render(
        <React.StrictMode>
          <App containerId={container.id} />
        </React.StrictMode>
      );
    });
  }
});
