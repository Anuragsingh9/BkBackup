import React from 'react';
import {Grid} from '@material-ui/core';
import _ from 'lodash';
import {reactLocalStorage} from 'reactjs-localstorage';
import BadgeIconUpload from './Common/BadgeIconUpload/BadgeIconUpload.js';
import EditLabel from './Common/EditLabel/EditLabel.js';
import VipRoleIcon from '../../../Svg/VipRoleIcon';
import TeamRoleIcon from '../../../Svg/TeamRoleIcon';
import ExpertRoleIcon from '../../../Svg/ExpertRoleIcon';
import './LabelSettings.css';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component allows the Pilot to modify the labels for the different system roles across the platform.
 * Pilot can also upload icon and images for Business Team, Experts, VIP which are displayed on the User Grid
 * component in Event Dashboard page.
 * Currently we are supporting
 * 1. English(EN)
 * 2. French(FR)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.getKeyData To get key data(business_team_icon, business_team_altImage, vip_icon,
 * vip_altImage, expert_icon, expert_altImage)
 * @param {LabelObj} props.labels Array of labels
 * @param {Function} props.update Function to update labels
 * @param {Function} props.callBack Function is used to go back
 * @param {Function} props.updateDesignSetting Function is used to update design setting
 * @returns {JSX.Element}
 * @constructor
 */
const LabelSettings = (props) => {
    const businessTeamIcon = props.getKeyData('business_team_icon');
    const businessTeamAltImage = props.getKeyData('business_team_altImage');
    const vipIcon = props.getKeyData('vip_icon');
    const vipAltImage = props.getKeyData('vip_altImage');
    const expertIcon = props.getKeyData('expert_icon');
    const expertAltImage = props.getKeyData('expert_altImage');
    const moderatorAltImage = props.getKeyData('moderator_altImage');

    const {checkGroupDisable} = props;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user update any label from labels customization section in design
     * setting. This will take updated label data(text and index) and replace it with current labels data  and pass
     * them to callback function 'update'(received from props) so that labels can change instantly every where on the
     * interface.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data is used to get labels
     * @param {Number} data.index Label index value
     * @param {LabelObj} data.value Label value
     */
    const getData = (data) => {
        props.labels.map((v, i) => {
            if (data.index == i) {
                const setData = {
                    "name": v.name,
                    "locales": data.value
                }
                props.update(setData);
            }
        })
    }

    const crtLang = reactLocalStorage.get("current_lang");

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will provide image uploader component to upload customized icon related to the roles.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} data Name of the role
     * @returns {JSX.Element}
     */
    const imageSection = (data) => {
        if (data == "business_team" || data == "expert" || data == "moderator" || data == "vip") {
            switch (data) {
                case "business_team":
                    return (
                        <Grid container xs={12} className="IconUpdateRow">
                            <Grid item xs={6}>
                                <BadgeIconUpload
                                    icon={businessTeamIcon}
                                    label={"Icon:"}
                                    changeIcon={true}
                                    callBack={props.callBack}
                                    updateDesignSetting={props.updateDesignSetting}
                                    defaultIcon={<TeamRoleIcon />}
                                    disabled={checkGroupDisable} />
                            </Grid>
                            <Grid item xs={6}>
                                <BadgeIconUpload
                                    customClass="altIconUpload"
                                    icon={businessTeamAltImage}
                                    label={"Alt Image:"}
                                    callBack={props.callBack}
                                    updateDesignSetting={props.updateDesignSetting}
                                    disabled={checkGroupDisable} />
                            </Grid>
                        </Grid>
                    )
                case "expert":
                    return (
                        <Grid container xs={12} className="IconUpdateRow">
                            <Grid item xs={6}>
                                <BadgeIconUpload icon={expertIcon} label={"Icon:"} changeIcon={true}
                                                 callBack={props.callBack}
                                                 updateDesignSetting={props.updateDesignSetting}
                                                 defaultIcon={<ExpertRoleIcon />} disabled={checkGroupDisable} />
                            </Grid>
                            <Grid item xs={6}>
                                <BadgeIconUpload
                                    customClass="altIconUpload"
                                    icon={expertAltImage}
                                    label={"Alt Image:"}
                                    callBack={props.callBack}
                                    updateDesignSetting={props.updateDesignSetting}
                                    disabled={checkGroupDisable} />
                            </Grid>
                        </Grid>
                    )

                case "vip"  :
                    return (
                        <Grid container xs={12} className="IconUpdateRow">
                            <Grid item xs={6}>
                                <BadgeIconUpload icon={vipIcon} label={"Icon:"} changeIcon={true}
                                                 callBack={props.callBack}
                                                 updateDesignSetting={props.updateDesignSetting}
                                                 defaultIcon={<VipRoleIcon />} disabled={checkGroupDisable} />
                            </Grid>
                            <Grid item xs={6}>
                                <BadgeIconUpload customClass="altIconUpload" icon={vipAltImage} label={"Alt Image:"}
                                                 callBack={props.callBack}
                                                 updateDesignSetting={props.updateDesignSetting}
                                                 disabled={checkGroupDisable} />
                            </Grid>
                        </Grid>
                    )
            }


        }
    }


    return (
        <div className="LabelSettingDivmain">
            <Grid container xs={12}>
                {props.labels &&
                props.labels.map((v, i) => (
                    <Grid container xs={12} className="FlexRow borderDiv">
                        <Grid item xs={3}>
                            {v.locales.map(v => {
                                return (
                                    crtLang.toLowerCase() == v.locale
                                        ? <p className='customPara customPara-2'> {v.value}</p>
                                        : ''
                                )
                            })}
                        </Grid>
                        <Grid item xs={8}>
                            <EditLabel
                                locales={v.locales}
                                getData={getData}
                                index={i}
                                update={props.update}
                                imageSection={imageSection(v.name)}
                                disabled={checkGroupDisable}
                            />
                        </Grid>
                    </Grid>
                ))}
            </Grid>
        </div>
    )
};

export default LabelSettings;