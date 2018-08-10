import React, {Component, Fragment} from 'react';
import axios from 'axios';
import {Link, Redirect} from "react-router-dom";

export default class Users extends Component {
    state = {
        users: []
    };

    async componentDidMount() {
        let users = await axios.get('https://jsonplaceholder.typicode.com/users');

        this.setState({users: users.data});
    }

    _logout = () => {
        sessionStorage.removeItem('token');

        this.props.history.push('/');
    };

    render() {
        if (!sessionStorage.getItem('token')) {
            return <Redirect to={'/login'}/>
        }

        const {users} = this.state;

        return (
            <Fragment>
                {users.map((user, key) => <p key={key}>{user.name}</p>)}
                <p>Return <Link to={'/'}>Home</Link>.</p>
                <button onClick={this._logout}>Logout</button>
            </Fragment>
        )
    }
}
