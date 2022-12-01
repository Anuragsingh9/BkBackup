import React from 'react';
import {Navigate, useLocation, useParams} from 'react-router-dom';
import routeAgent from "../agents/routeAgent";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed for assigning a wrapper layout for a specific component and propagates the
 * router props to child components.<br>
 * If a user is not authenticated or logged in, it will be redirected to initial route.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {React.Component} Component Child component to render
 * @param {boolean} authed To indicate user is authenticated
 * @param {Object}  rest props to pass on route component
 * @returns {JSX.Element}
 * @constructor
 */
function PrivateRoute({children, authed, ...rest}) {
    const {search} = useLocation();
    const query = React.useMemo(() => new URLSearchParams(search), [search]);
    const accessCode = query.get('access_code');

    const params = useParams();

    return localStorage.getItem('accessToken')
        ? children({...rest, match: {params: params}})
        : <Navigate to={{pathname: routeAgent.routes.QUICK_LOGIN(params.event_uuid) }}
        />

}

export default PrivateRoute;