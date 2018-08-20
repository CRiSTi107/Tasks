import React, {Component} from 'react';
import axios from 'axios';
import '../../css/Auth.css';
import { Redirect } from "react-router-dom";
import { Link } from "react-router-dom";

export default class ForgotPassword extends Component
{
    state = {
        email: '',
        error: ''
    };

    _onChange = (e) => {
        const {name, value} = e.target;

        this.setState({
            [name]: value
        });
    };

    _reset = async () => {
        const {email} = this.state;

        const response = await axios.post(process.env.REACT_APP_API_URL + 'forgot-password', {
            email
        });

        if (response && response.data.responseType === 'success') {
            this.props.history.push('/users');
        } else if(response) {
            this.setState({ error: response.data.errorMessage });
        }
    };

    render() {
        const {email} = this.state;

        //check if user is already logged in
        if(sessionStorage.getItem('token')) {
            return <Redirect to={'/'}/>;
        }

        return (
            <div className="page">
                <div className="form">
                    <p className={'title'}>Forgot Password!</p>

                    <p className={'input'}>Email</p>
                    <input type={'text'} name={'email'} value={email} onChange={this._onChange}/>

                    <p className={'message error'}>{this.state.error}</p>

                    <button onClick={this._reset}>Reset</button>

                    <p className="message">Remember password? <Link to={'/login'}>Login</Link></p>
                </div>
            </div>
        )
    }

}
