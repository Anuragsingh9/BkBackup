import React from 'react';
import {Route} from 'react-router-dom';
import AuthLayout from '../views/NewInterFaceRegister/AuthLayout/AuthLayout2.js';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component for assigning a wrapper layout for a specific component and propagates the router props
 * to child components.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {React.Component} Component Child component to render
 * @param {boolean} authed To indicate user is authenticated
 * @param {Object}  rest props to pass on route component
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
function SimpleAuthRoute({children: Component, authed, ...rest}) {
    return (
        <AuthLayout {...rest} Child={Component} />
    )
}

export default SimpleAuthRoute