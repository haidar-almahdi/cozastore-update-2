#!/usr/bin/env node
const fs = require('fs-extra');
const path = require('path');

const vendorPath = path.join(__dirname, 'public', 'vendor');

// Create vendor directory if it doesn't exist
fs.ensureDirSync(vendorPath);

// Define assets to copy
const assets = {
    'bootstrap': {
        from: 'node_modules/bootstrap/dist',
        to: 'bootstrap'
    },
    'jquery': {
        from: 'node_modules/jquery/dist',
        to: 'jquery'
    },
    'animsition': {
        from: 'node_modules/animsition/dist',
        to: 'animsition'
    },
    'select2': {
        from: 'node_modules/select2/dist',
        to: 'select2'
    },
    'daterangepicker': {
        from: 'node_modules/daterangepicker',
        to: 'daterangepicker'
    },
    'slick-carousel': {
        from: 'node_modules/slick-carousel/slick',
        to: 'slick'
    },
    'magnific-popup': {
        from: 'node_modules/magnific-popup/dist',
        to: 'MagnificPopup'
    },
    'perfect-scrollbar': {
        from: 'node_modules/perfect-scrollbar/dist',
        to: 'perfect-scrollbar'
    }
};

// Copy each asset
Object.entries(assets).forEach(([name, { from, to }]) => {
    const sourcePath = path.join(__dirname, from);
    const destPath = path.join(vendorPath, to);
    
    if (fs.existsSync(sourcePath)) {
        fs.copySync(sourcePath, destPath);
        console.log(`Copied ${name} to ${to}`);
    } else {
        console.error(`Source path not found for ${name}: ${sourcePath}`);
    }
}); 