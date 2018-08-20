import React, {Component} from 'react';
import {Redirect, Link} from 'react-router-dom';
import {Button} from 'reactstrap';
import '../../css/Misc.css';

export default class Header extends Component {
    state = {
        redirect: false
    };

    _logout = () => {
        sessionStorage.removeItem('token');

        this.setState({
            redirect: true
        });
    };

    render() {
        if (this.state.redirect) {
            return <Redirect to={'/login'}/>;
        }

        return (
            <div className={'header'}>
                <ul>
                    <li><Link to={'/'}>Home</Link></li>
                    <li><Link to={'/users'}>Users</Link></li>
                    <li className={'user'}>
                        <Button color="secondary" size="sm" onClick={this._logout}>Logout</Button>
                    </li>
                </ul>

            </div>
        );
    }
}