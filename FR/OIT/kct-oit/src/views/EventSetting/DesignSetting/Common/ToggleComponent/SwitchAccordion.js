import React, {useEffect, useState} from "react";
import Switch from "@material-ui/core/Switch";
import {Button, Grid} from "@material-ui/core";
import ProfileUpload from "../../ProfileUpload/ProfileUpload";
import _ from "lodash";
import "./SwitchAccordion.css";
import ModalBox from "../ModalBox/Modal";
import {useParams} from "react-router-dom";
import ImageUploader from "../../../../Common/ImageUploader/ImageUploader";
import {confirmAlert} from "react-confirm-alert";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common switch accordion component which is used to render all sub/child design setting's
 * section.When it is turned on then we can see its child content(settings option) otherwise we can't see them.
 * <br>
 * <br>
 * Save setting, cancel and reset to primary colors options are also managing from it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received design setting related values and functions from it's parent component
 * @param {String} props.SubHeading Small heading placed at top.
 * @param {Function} props.callBack Function for to take current value.
 * @param {Function} props.callBackCancel Function for cancel button when user want to go back
 * @param {Function} props.child Function which return child components when main switch is ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top
 * @param {Icon} props.icon Icon to update the value
 * @param {Function} props.resetColorHandler Function to reset all colors of primary color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const SwitchAccordion = (props) => {
    const groupLogo = props.getKeyData("group_logo");

  const [switchState, setSwitch] = useState(false);
  const [groupSettingSwitch, setGroupSettingSwitch] = useState(false);
  const [accordion, setAccordion] = useState({});
  const [resetColor, setresetColor] = useState({
        field: "",
        value: "",
    });
    const [open, setOpen] = React.useState(false);
    const [checkGroupDisable, setCheckGroupDisable] = useState(false);
    const {gKey} = useParams();
    let ownCustomizationKey = _.find(
        props.graphicSetting,
        (setting) => setting.field == "group_has_own_customization"
    );
    let applyCustomizationKey = _.find(
        props.graphicSetting,
        (setting) => setting.field == "apply_customisation"
    );

    //useEffect(() => {
      //  if (props.dataKey == "apply_customisation" && props.eventLogoDefaultData) {
        //    setLogoIsDefault(props.eventLogoDefaultData)
      //  }
   //   // console.log('showCrossIcon--', props)
  // }, [props.group_logo])
    const keyName = "group_logo"

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handle image file change and uploads on  content setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} fileObj Object of file
     */
    const fileChange = (fileObj) => {
        if (fileObj) {
            // setFile(fileObj);
            // setFileName(fileObj.name);
            props.callBack(
                {
                    field: props.keyName,
                    value: fileObj
                }
            )
        }

    }
    const deleteImage = () => {
        confirmAlert({
            message: 'Are you sure, you want to delete?',
            confirmLabel: 'Confirm',
            cancelLabel: 'Cancel',
            buttons: [
                {
                    label: 'Yes',
                    onClick: () => {
                        props.updateDesignSetting({field: props.keyName, value: null});
                    }
                },
                {
                    label: 'No',
                    onClick: () => {
                        return null
                    }
                }
            ],

        })
    }

    useEffect(() => {
        if (props.dataKey) {
            const accordionData = props.getKeyData(props.dataKey);
            const {value} = accordionData;
            setAccordion(accordionData);
            if (value) {
                setSwitch(true);
            } else {
                setSwitch(false);
            }
        }
        if (props.AllowDesignSetting == 1) {
            setCheckGroupDisable(false);
            if (ownCustomizationKey.value == 1) {
                setGroupSettingSwitch(true);
            } else {
                setGroupSettingSwitch(false);
            }
        } else {
            setCheckGroupDisable(true);
            setGroupSettingSwitch(false);
        }
        // props.group_has_own_customization == 1 ? setGroupSettingSwitch(true) : setGroupSettingSwitch(false);
        // (props.AllowDesignSetting == 0 && ownCustomizationKey.value == 1 ) ? setGroupSettingSwitch(true) : setGroupSettingSwitch(false);
        checkDisable();
    }, [props.dataKey, props.group_has_own_customization]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle the open state of 'reset to primary' modal box.This will trigger when user
     * click on 'reset to primary' option in some sub/child design setting's sections.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleOpen = () => {
        props.resetColorHandler(1, props.dataKey);
        setOpen(true)
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle the close state of 'reset to primary' modal box.This will trigger when
     * user click on 'cancel' option from 'reset to primary' modal box.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleClose = () => {
        props.resetColorHandler(0, props.dataKey);
        setOpen(false);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will call 'updateDesignSetting' method to reset all colors of a section to primary
     * colors and close the 'reset to primary' modal box.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const resetConfirmHandler = () => {
        props.updateDesignSetting();
        handleClose();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user change toggle switch value(ON/OFF) of switch accordian
     * component.This will get latest state(ON/OFF) of the switch and call 'updateDesignSetting' method.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const triggerData = (e) => {
        const value = e.target.checked ? 1 : 0;
        const accordionData = props.getKeyData(props.dataKey);
        const {field} = accordionData;
        setSwitch(e.target.checked)
        props.updateDesignSetting({field: field, value: value});
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     *  @description This method handle switch values for whole group settings in design settings
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const triggerGroupData = (e) => {
        const value = e.target.checked ? 1 : 0;
        const accordionData = props.getKeyData(props.dataKey);

        const {field} = accordionData;
        // setSwitch(e.target.checked)
        setGroupSettingSwitch(e.target.checked);

        props.updateDesignSetting({field: field, value: value});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the value of apply_customization key(which decide who can modify all
     * design settings for this current logged-in account).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {Boolean}
     */
    const checkDisable = () => {
        const mainKey = props.getKeyData("apply_customisation");
        // const groupDesignSetting = props.setgroupDesignSetting()

        let disabled =
            _.has(mainKey, ["value"]) &&
            mainKey.value == 0 &&
            _.has(accordion, ["field"]) &&
            accordion.field != "apply_customisation";

        let groupDisabled =
            props.AllowDesignSetting == 0
                ? true
                : ownCustomizationKey.value == 0
                    ? true
                    : applyCustomizationKey.value == 0
                        ? disabled
                        : "";

        return gKey !== "default" ? groupDisabled : disabled;
    };
    const mainaApplyKey = props.getKeyData("apply_customisation");
    // add disabled condition state in it and pass to props.child in accordian component
    const additionalProps = {
        ...props,
        checkGroupDisable: checkDisable()
    }
    return (
        <Grid container xs={12} className="SwitchDivRow">
            <Grid item xs={11} className="TogglerRow">
                <Grid container className="ToggleMainFlex">
                    <Grid item className="Flex-1">
                        {props.icon}
                    </Grid>
                    <Grid item className="Flex-2">
                        {props.heading}
                        <span className="SmallSubHeading">{props.SubHeading}</span>
                    </Grid>
                </Grid>
            </Grid>
            <Grid item xs={1}>
                {_.has(props, "ownCustomization") ? (
                    <Switch
                        checked={groupSettingSwitch}
                        color="primary"
                        disabled={checkGroupDisable}
                        onChange={(e) => {
                            triggerGroupData(e);
                        }}
                    />
                ) : (
                    <Switch
                        checked={switchState}
                        color="primary"
                        disabled={checkDisable()}
                        onChange={(e) => {
                            triggerData(e);
                        }}
                    />
                )}
            </Grid>
            {switchState &&
            _.has(props, "child") &&
            _.has(mainaApplyKey, ["value"]) &&
            mainaApplyKey.value == 1 ? (
                <div className="SwitchContentChildDiv">
                    {props.child(additionalProps)}
                    {props.hideButtons && props.hideButtons === true ? (
                        ""
                    ) : (
                        <div className="SwitchContentChildDiv-2">
                            <Button
                                variant="outlined"
                                color="primary"
                                disabled={checkDisable()}
                                onClick={props.callBackCancel}
                            >
                                Cancel
                            </Button>

                            <Button
                                variant="contained"
                                color="primary"
                                disabled={checkDisable()}
                                onClick={() => {
                                    props.updateDesignSetting();
                                }}
                            >
                                Save
                            </Button>
                            {props.color_modal == true ? (
                                <ModalBox
                                    open={open}
                                    handleOpen={handleOpen}
                                    handleClose={handleClose}
                                    btn_txt={`Reset to Primary Colors`}
                                    // onclick={() => { props.updateDesignSetting() }}
                                    onclick={checkDisable() ? "" : resetConfirmHandler}
                                    // reset_color={props.reset_color}
                                    disabled={checkDisable()}
                                />
                            ) : (
                                ""
                            )}
                        </div>
                    )}
                </div>
            ) : (
                _.has(accordion, ["field"]) &&
                _.has(props, "child") &&
                accordion.field == "apply_customisation" && (
                    <div className="SwitchContentChildDiv">

                        <Grid container xs={12} className="FlexRow QuickSettingRow">
                            <Grid item xs={3}>
                                <p className='customPara customPara-2 QuickSettingLabel'> Event Logo : </p>
                            </Grid>
                            <Grid item className='eventLogoUploader'>
                                <ProfileUpload
                                    group_logo={groupLogo}
                                    callBack={props.callBack}
                                    updateDesignSetting={props.updateDesignSetting}
                                    logoIsDefault={props.eventLogoDefaultData} />

                            </Grid>

                        </Grid>
                        <div className="SwitchContentChildDiv-2">
                            <Button
                                variant="outlined"
                                color="primary"
                                disabled={checkDisable()}
                                onClick={props.callBackCancel}
                            >
                                Cancel
                            </Button>

                            <Button
                                variant="contained"
                                color="primary"
                                disabled={checkDisable()}
                                onClick={() => {
                                    props.updateDesignSetting();
                                }}
                            >
                                Save
                            </Button>
                        </div>
                    </div>
                )
            )}
        </Grid>
    );
};

export default SwitchAccordion;
