import React, {Component} from 'react';
import Header from './Header';
import Footer from './Footer';
import {Container} from 'reactstrap';
import {Redirect} from "react-router-dom";
import axios from 'axios';
import qs from 'qs';
import '../../css/Misc.css';

export default class Layout extends Component {
    render() {
        if (!sessionStorage.getItem('token')) {
            return <Redirect to={'/login'}/>
        }

        axios.interceptors.request.use((request) => {
            if (request.data && request.headers['Content-Type'] === 'application/x-www-form-urlencoded') {
                request.data = qs.stringify(request.data);
            }
            return request;
        });

        axios.defaults.headers.common.Authorization = 'Bearer ' + sessionStorage.getItem('token');

        return (
            <div className={'layout'}>
                <Header/>
                <div className={'content container users'}>
                    {this.props.children}
                </div>
                <Footer/>
            </div>
        );
    }
}