const path = require('path');
module.exports = {
  publicPath: '/',
  devServer: {
    host: '0.0.0.0',
    port: 8080,
    allowedHosts: [
      'app.nii.test',
      'localhost'
    ]
  }
}
