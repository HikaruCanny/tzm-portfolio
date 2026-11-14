// resources/js/bootstrap.ts
import axios from 'axios';

// Set default headers for AJAX requests
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// If you use Laravel Echo or any other global libraries, initialize them here.
// For now this file only configures Axios, which satisfies the import in app.ts.

