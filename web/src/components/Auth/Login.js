import React, {Component} from 'react';
import axios from 'axios';
import '../../css/Auth.css';
import { Link } from "react-router-dom";
import { Redirect } from 'react-router-dom'

export default class Login extends Component {
    state = {
        email: '',
        password: '',
        error: ''
    };

    _onChange = (e) => {
        const {name, value} = e.target;

        this.setState({
            [name]: value
        });
    };

    _login = async () => {
        const {email, password} = this.state;

        const response = await axios.post(process.env.REACT_APP_API_URL + 'login', {
            email, password
        });

        if (response && response.data && response.data.data) {
            sessionStorage.setItem('token', response.data.data.jwt);
            this.props.history.push('/users');
        } else if(response) {
            this.setState({ error: response.data.errorMessage });
        }
    };

    render() {
        const {email, password} = this.state;

        //check if user is already logged in
        if(sessionStorage.getItem('token')) {
            return <Redirect to={'/'}/>
        }

        return (
            <div className="page">
                <div className="form">
                    <p className={'title'}>Login!</p>

                    <p className={'input'}>Email</p>
                    <input type={'text'} name={'email'} value={email} onChange={this._onChange}/>

                    <p className={'input'}>Password</p>
                    <input type={'password'} name={'password'} value={password} onChange={this._onChange}/>

                    <p className={'message error'}>{this.state.error}</p>

                    <button onClick={this._login}>Login</button>

                    <p className="message">Not registered? <Link to={'/register'}>Create an account</Link></p>
                    <p className="message">Forgot password? <Link to={'/forgot-password'}>Reset</Link></p>
                </div>
            </div>
        )
    }
}
