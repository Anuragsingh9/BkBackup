import React from 'react';
import Header from '../views/Header/Header.js';
import Footer from '../views/Footer/Footer.js';
import { Container } from '@material-ui/core';
import { Route } from 'react-router-dom';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for assigning layout wrapper for specific components.
 * And propagates the router props to child components.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {React.Component} Component Component is used for reset, forget and sign-in 
 * @param {Boolean} authed User is authenticated or not
 * @param {Boolean} rest.exact Check current url status
 * @param {String} rest.path Current page's path
 * @param {String} rest.name Page name
 * @param {Object} rest.location Route related object
 * @param {Object} rest.computedMatch Match object
 * @param {Boolean} rest.computedMatch.isExact Boolean to verify path match
 * @param {Object} rest.computedMatch.params Additional route related parameter if required
 * @param {String} rest.computedMatch.path Path
 * @param {String} rest.computedMatch.url URL of the page
 * @returns {JSX.Element}
 * @constructor
 */
function SimpleRoute ({component: Component, authed, ...rest}) {
    return (
      <Route
        {...rest}
        exact
        render={(props) => {
            return(
                <React.Fragment>
                   
                    <Container>
                        <Component {...props}/>
                    </Container>
                    <Footer {...props} />
                </React.Fragment>
            )
        }}
      />
    )
}
export default SimpleRoute