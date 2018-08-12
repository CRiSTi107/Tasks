import React, {Component, Fragment} from 'react';
import axios from 'axios';
import {Link, Redirect} from "react-router-dom";

export default class Users extends Component {
    state = {
        users: [],
        error: ''
    };

    async componentDidMount() {

        let config = {
            headers: {'Authorization': "Bearer " + sessionStorage.getItem('token')}
        };

        let users = await axios.get('http://api.task.local/v1/admin/users', config);

        if(users.data.responseType === 'success') {
            this.setState({users: users.data.data});
        } else {
            alert(users.data.errorMessage);
        }

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
