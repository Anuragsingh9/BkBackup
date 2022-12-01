import React from 'react';
import Helper from "../Helper";
import Constants from '../Constants';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is responsible for preparing routes according to route type(name) provided in props
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {String} props.type Name of the route which needs to be prepared
 * @param {Object} props.queryData Data object which will be sent as query param
 * @returns {{eventAnalytics: (function(*, *): string)}}
 */
let useRouteService = (props) => {

    console.log('route service', props.type)
    const {gKey} = props.match.params;


        const routes = {
            eventAnalytics : (eventUuid,queryData) =>`/${gKey}/v4/event/analytics/${eventUuid}?${Helper.toQueryParam(queryData)}`,
        }
        return routes;
}


export default useRouteService;