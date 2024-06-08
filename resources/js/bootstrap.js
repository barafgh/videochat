import axios from 'axios';

// Setup Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import Echo configuration
import './echo';
