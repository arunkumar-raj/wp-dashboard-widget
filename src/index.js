import { render } from '@wordpress/element';
import App from "./App";
import './style/main.scss';

let div_app = document.getElementById('rankmath-widget');
let site_url = div_app.getAttribute('data-url');
render( <App SITE_URL={site_url} />, div_app );