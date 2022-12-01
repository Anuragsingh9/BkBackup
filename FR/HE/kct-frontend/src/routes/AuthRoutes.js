import React from 'react';
import {Route} from 'react-router-dom';
import AuthLayout from '../views/NewInterFaceRegister/AuthLayout/AuthLayout.js';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed for assigning wrapper layout for specific components(which is managed as a
 * page component eg- dashboard page/event list page) and propagates the router props to child components.
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
function AuthRoute({component: Component, authed, ...rest}) {
    return (
        <Route
            {...rest}
            exact
            render={(props) => {
                return (<AuthLayout {...props} Child={Component} />)
            }}
        />
    )
}

export default AuthRoute