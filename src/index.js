import { render } from '@wordpress/element';
import App from "./App";
import './style/main.scss';

let div_app = document.getElementById('rankmath-widget');
render( <App/>, div_app );