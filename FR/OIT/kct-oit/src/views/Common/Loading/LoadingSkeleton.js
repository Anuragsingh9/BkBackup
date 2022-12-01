import React from 'react';
import LinearProgress from '@material-ui/core/LinearProgress';


/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common Component for showing Loading element until the background process completes(eg - API
 * Call for get data/submit data)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {Boolean} props.loading To handle the loader status
 * @return {JSX.Element}
 * @constructor
 */
const LoadingSkeleton = (props) => {

    return (
        <React.Fragment>
            {props.loading ?
                <>
                    {props.skeleton}
                </>
                :
                props.children
            }
        </React.Fragment>
    )
}

export default LoadingSkeleton;