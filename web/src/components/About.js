import React, {Component} from 'react';
import {Link} from "react-router-dom";

export default class About extends Component {
    render() {
        return (
            <div>
                <p> Hello, again friends! This is the about page.</p>
                <p>Proceed to the <Link to={'/users'}>users list</Link>.</p>
                <p>Return <Link to={'/'}>Home</Link>.</p>

            </div>
        )
    }
}
