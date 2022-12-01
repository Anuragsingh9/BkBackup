import React from 'react';
import _ from 'lodash';
import {Button, Grid} from '@material-ui/core';
import ProfileUpload from '../ProfileUpload/ProfileUpload';
import './EventLogo.css';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component of event logo which provide specific column configuration to match other
 * section's structure for it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {JSX.Element}
 * @constructor
 */
const EventLogo = () => {

    return (
        <div className="QuickDesignDiv">
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow LogoUploadDiv">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Event Logos : </p>
                    </Grid>
                    <Grid item>
                        {/* <ProfileUpload group_logo={groupLogo} callBack={props.callBack} /> */}
                        <ProfileUpload
                        />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    )

}

export default EventLogo;
