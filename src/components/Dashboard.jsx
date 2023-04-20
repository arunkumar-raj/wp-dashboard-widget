import React, { Component } from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

class Dashboard extends Component {

	constructor( props ) {
		super(props);
		this.state = {chartdata:null};
	}

	componentDidMount = () => {
		this.handleFetch();
	}

	handleChange = extId => (event) => {
		let date_value = event.target.value;
		this.handleFetch( date_value );
	} 

	handleFetch = ( date_value = null ) => {
		if( date_value === null ){
			date_value = 7;
		}
		fetch(this.props.SITE_URL+'/wp-json/wprm-dashboard/v1/getchart/'+date_value)
		.then(Response => { return Response.json(); })
		.then(data=>{ this.setState({chartdata:data}) })
	}

	render() {
		if(this.state.chartdata !== null){
			return (
				<div className='dashboard_widget'>
					<div className='dashboard_dropdown'>
						<select onChange={this.handleChange()}>
							<option value={7}>Last 7 Days</option>
							<option value={15}>Last 15 Days</option>
							<option value={30}>Last 1 Month</option>
						</select>
					</div>
					<ResponsiveContainer width="100%" height="100%"  aspect={1}>
						<LineChart
							data={this.state.chartdata}
							margin={{top: 5,right: 30,left: 0,bottom: 5}}
						>
							<CartesianGrid strokeDasharray="3 3" />
							<XAxis dataKey="name" />
							<YAxis />
							<Tooltip />
							<Legend />
							<Line type="monotone" dataKey="pv" stroke="#8884d8" activeDot={{ r: 8 }} />
							<Line type="monotone" dataKey="uv" stroke="#82ca9d" />
						</LineChart>
					</ResponsiveContainer>
				</div>
			);
		}else{
			return 'No data to show';
		}
		
	}
	
}
export default Dashboard