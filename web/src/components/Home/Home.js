import React, {Component} from 'react';
import {Link} from "react-router-dom";
import {Header} from "../Misc/Header";
import Layout from "../Misc/Layout";

export default class Home extends Component {
    render() {
        return (
            <Layout>
                <div>
                    <p className={'title'}>Hello, friends!</p>
                    <p>Go to <Link to={'/login'}>Log In</Link> page.</p>
                    <p>Go to <Link to={'/register'}>Register</Link> page.</p>
                    <p>Go to <Link to={'/forgot-password'}>Forgot Password</Link> page.</p>
                    <p>Go to <Link to={'/users'}>Users</Link> page.</p>
                </div>
            </Layout>
        )
    }
}
