import React, {Component} from 'react';
import axios from 'axios';
import '../../css/Auth.css';
import { Redirect } from "react-router-dom";
import { Link } from "react-router-dom";

export default class Register extends Component
{
    state = {
        name: '',
        email: '',
        password:'',
        password_confirm: '',
        error: ''
    };

    _onChange = (e) => {
        const {name, value} = e.target;

        this.setState({
            [name]: value
        });
    };

    _register = async () => {
        const {name, email, password, password_confirm} = this.state;

        if(password !== password_confirm)
        {
            this.setState({ error: 'Passwords do not match.' });
            return;
        }

        const response = await axios.post(process.env.REACT_APP_API_URL + 'register', {
            name, email, password
        });

        console.log(response);

        if (response && response.data.responseType === 'success') {
            this.props.history.push('/users');
        } else if(response) {
            this.setState({ error: response.data.errorMessage });
        }
    };

    render() {
        const {name, email, password, password_confirm} = this.state;

        //check if user is already logged in
        if(sessionStorage.getItem('token')) {
            return <Redirect to={'/'}/>;
        }

        return (
            <div className="page">
                <div className="form">
                    <p className={'title'}>Register!</p>

                    <p className={'input'}>Name</p>
                    <input type={'text'} name={'name'} value={name} onChange={this._onChange}/>

                    <p className={'input'}>Email</p>
                    <input type={'text'} name={'email'} value={email} onChange={this._onChange}/>

                    <p className={'input'}>Password</p>
                    <input type={'password'} name={'password'} value={password} onChange={this._onChange}/>

                    <p className={'input'}>Confirm Password</p>
                    <input type={'password'} name={'password_confirm'} value={password_confirm} onChange={this._onChange}/>

                    <p className={'message error'}>{this.state.error}</p>

                    <button onClick={this._register}>Register</button>

                    <p className="message">Already registered? <Link to={'/login'}>Log In</Link></p>
                </div>
            </div>
        )
    }

}
