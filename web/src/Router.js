import React, {Component} from 'react';
import {BrowserRouter, Switch, Route} from 'react-router-dom';

import Home from './components/Home';
import About from './components/About';
import Users from './components/Users';
import Login from './components/Login';

export default class Router extends Component {
    render() {
        return (
            <BrowserRouter>
                <Switch>
                    <Route exact path="/" component={Home}/>
                    <Route exact path="/about" component={About}/>
                    <Route exact path="/users" component={Users}/>
                    <Route exact path="/login" component={Login}/>
                </Switch>
            </BrowserRouter>
        );
    }
}
