import React from 'react';
import Header from '../views/Header/Header.js';
import Footer from '../views/Footer/Footer.js';
import {Route} from 'react-router-dom';
import SideBar from '../views/v4/SideBar/SideBar.js';
import "./Layout.css"
import CustomContainer from '../views/Common/CustomContainer/CustomContainer.js';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for assigning layout wrapper for specific components and propagates the router props to child
 * components.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {React.Component} Component Child component to render for a specific route
 * @param {Boolean} authed To indicate user is authenticated
 * @param {Object} rest Route related props
 * @param {String} rest.path Current page's path
 * @param {String} rest.name Page name
 * @param {Object} rest.location Route related object
 * @param {Object} rest.computedMatch Match object
 * @param {Boolean} rest.computedMatch.isExact Boolean to verify path match
 * @param {Object} rest.computedMatch.params Additional route related parameter if required
 * @param {String} rest.computedMatch.path Path
 * @param {String} rest.computedMatch.url URL of the page
 * @return {JSX.Element}
 * @constructor
 */
function AuthRoute({component: Component, authed, ...rest}) {
    return (
        <Route
            {...rest}
            exact
            render={(props) => {
                return (
                    <React.Fragment>
                        {rest.headerLoad !== false && <Header {...props} />}
                        <div className='bodyLayout'>
                            <div className='sidebarWrap'>
                                <SideBar {...props} ></SideBar>
                            </div>
                            <CustomContainer className='bodyWrap'>
                                <Component {...props} {...rest} />
                                <Footer {...props} />
                            </CustomContainer>
                        </div>
                    </React.Fragment>
                )
            }}
        />
    )
}

export default AuthRoute