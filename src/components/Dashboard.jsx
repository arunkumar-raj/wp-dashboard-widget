import React, { Component, useEffect, useState } from 'react';
import { SelectControl } from '@wordpress/components';
import {__} from "@wordpress/i18n";
import apiFetch from '@wordpress/api-fetch';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

const Dashboard = () =>{
	const [chartdata, setChartdata] = useState(null);
	const [sdate, setSdate] = useState(7);
	useEffect(() => {
		handleChange();
	}, []);

	const DateSelect = () => {
		return (
			<SelectControl
				value={ sdate }
				options={ [
					{ label: __('Last 7 Days','rankmath'), value: '7' },
					{ label: __('Last 15 Days','rankmath'), value: '15' },
					{ label: __('Last 1 Month','rankmath'), value: '30' },
				] }
				onChange={ ( newSdate ) => handleChange( newSdate ) }
				__nextHasNoMarginBottom
			/>
		);
	};
	
	const handleChange = (date_value = null) => {
		if( date_value === null ){
			date_value = 7;
		}else{
			setSdate(date_value);
		}
		apiFetch( { path:'/wprm-dashboard/v1/getchart/'+date_value } ).then( ( data ) => {
			setChartdata(data);
		});
	} 
	
	if( chartdata !== null ){
		return (
			<div className='dashboard_widget'>
				<div className='dashboard_dropdown'>
					{DateSelect()}
				</div>

				<ResponsiveContainer width="100%" height="100%"  aspect={1}>
					<LineChart
						data={chartdata}
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
		return __('No data to show','rankmath');
	}
}

export default Dashboard;
