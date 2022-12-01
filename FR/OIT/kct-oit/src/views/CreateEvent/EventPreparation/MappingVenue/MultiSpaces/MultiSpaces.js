import React, {useEffect, useState} from 'react';
import Radio from '@material-ui/core/Radio';
import {useAlert} from 'react-alert';
import {confirmAlert} from 'react-confirm-alert';
import RadioGroup from '@material-ui/core/RadioGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormControl from '@material-ui/core/FormControl';
import {Grid, TextField,} from '@material-ui/core';
import Button from '@material-ui/core/Button';
import {Field, reduxForm} from 'redux-form';
import {connect} from 'react-redux';
import {useTranslation} from 'react-i18next';
import SliderSPersons from "../SpaceSlider/SpaceSlider"
import eventAction from '../../../../../redux/action/apiAction/event';
import Helper from '../../../../../Helper';
import UserAutocomplete from '../../../../Common/UserAutoComplete/UserAutoComplete';
import { withAlert } from 'react-alert';
import _ from 'lodash';
import Constants from "../../../../../Constants";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is used to monitor user inputs and throw error if the input values are not matching the
 * proper criteria(eg - length more then 12, less then 1000)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the multi space values
 * @param {String} values.space_name Space name
 * @param {String} values.max_capacity Space user max capacity
 * @param {String} values.space_host Space host name
 * @return {Object}  Different type of error message objects
 */
const validate = (values) => {
    const errors = {};
    const requiredFields = [
        "space_name",
        // "space_short_name",
        "max_capacity",
        "space_host",
    ];
    requiredFields.forEach((field) => {
        if (!values[field]) {
            errors[field] = "Required";
        }
    });
    if (values['max_capacity'] < Constants.space.MIN_CAPACITY) {
        errors['max_capacity'] = 'Value should be between 12 and 1000';
    }
    if (values['max_capacity'] > Constants.space.MAX_CAPACITY) {
        errors['max_capacity'] = 'Value should be between 12 and 1000';
    }
    if (values["space_name"] && values.space_name.length > 14) {
        errors["space_name"] =
            "The Event space Line 1 of the event must not exceed 14 characters ";
    }
    if (values["space_short_name"] && values.space_short_name.length > 14) {
        errors["space_short_name"] =
            "The Event space Line 2 of the event must not exceed 14 characters ";
    }

    return errors;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component structure for a input field to render input box for header line 1 & header
 * line 2.This will take data(from parameter where it called) which is necessary to render relative text fields.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {String} label Label of space
 * @param {String} defaultValue Default value of input box
 * @param {Boolean} invalid Enter value is invalid
 * @param {Boolean} touched To indicate if the input box is touched or not
 * @param {String} error Error message from input box
 * @param {String} custom Custom text of input box
 *
 * @returns {JSX.Element}
 */
const renderTextField = (
    { input, value, label, defaultValue, meta: { invalid, touched, error }, ...custom },
) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={value}
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}
                {...input}
                {...custom}
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component structure for a input field to render input box for space capacity input box.
 *  This will take data(from parameter where it called) which is necessary to render relative number field.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {Object} inputProps Object that contains Input props for input box if any.
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Object} invalid Object that contain message of "Enter value is invalid" in key value pair
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message object for input box
 * @param {Object} custom Custom props (if any) to render on input box
 * @returns {JSX.Element}
 */
const renderNumberField = ({
                               input, value, inputProps, label, defaultValue, meta: {invalid, touched, error}, ...custom
                           }) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={value}
                type="number"
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}
                inputProps={inputProps}
                {...input}
                {...custom}
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage edit sapce data if venue type is equals to multiple space. With
 * additional settings to select a Space Host, set number of users that can access the space and making it VIP only for
 * multiple spaces.Users can also set scenery (background image and color) and update Event header lines.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props
 * @param {Function} props.addSpace To add new space in an event
 * @param {Function} props.allowAdd To allow add user or not
 * @param {String} props.eventId Event uuid of the event
 * @param {EventRoleLabel} props.eventRoleLabels All event roles labels
 * @param {Number} props.eventRolelabelCustomized To check if customised event role labels need to shown or not
 * @param {Object} props.eventStatus Status of the event in time
 * @param {String} props.eventStatus.is_past To check if it is past event
 * @param {String} props.eventStatus.is_live To check if it is live event
 * @param {String} props.eventStatus.is_future To check if it is future event
 * @param {Function} props.getSpaces To get all spaces data of an event
 * @param {Function} props.removeSpace Method to remove or delete a space
 * @param {Function} props.updateSpaces Method to update a space of an event
 * @param {String} props.venueType Type of venue means event is mono space event or multi space event
 * @returns {JSX.Element}
 * @constructor
 */
var MultiSpaces = (props) => {
    const {handleSubmit, pristine, reset, submitting, initialize, accessMode} = props;
    const {t} = useTranslation(["mapping", "notification", "eventList", "confirm", "labels",
    ]);
    //Venue Type
    const MONO_SPACE = "1";
    const MULTI_SPACE = "2";

    const alert = useAlert();

    const [spaceLines, setSpaceLines] = useState({
        space_name: "",
        space_short_name: "",
        spaceHost: "",
        max_capacity: "",
        spaceId: "",
        isVip: "",
        eventState: {
            is_future: 0,

            is_live: 0,

            is_past: 0,
        },
    });
    const [showSpaceLine, setShowSpaceLine] = useState(false);
    const [spaceHostId, setSpaceHostId] = useState();
    const [hostName, setHostName] = useState([]);
    const [vip, setVip] = useState(0);
    const [spaceData, setSpaceData] = useState();
    const [eventUuid, setEventUuid] = useState();
    const [eventStatus, setEventStatus] = useState();
    const [firstData, setFirstData] = useState({
        space_name: "",
        space_short_name: "",
        spaceHost: "",
        max_capacity: "",
        spaceId: "",
        eventState: {},
    });
    const [firstSelectedHost, setFirstSelectedHost] = useState();
    const [disabled, setDisabled] = useState(false);

    useEffect(() => {
        let multiSpacesData = [];
        props.multiSpaces.map((v) => {
            if (props.venueType === MULTI_SPACE) {
                multiSpacesData.push(v);
            } else if (props.venueType === MONO_SPACE && v.is_default === 1) {
                multiSpacesData.push(v);
            }
        });
        setSpaceData(multiSpacesData);
        setEventStatus(props.eventStatus);
    }, []);

    useEffect(() => {
        props.setFormOpenStatus(showSpaceLine);
    }, [showSpaceLine]);

    useEffect(() => {
        setEventUuid(props.eventId)
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user input max capacity(no of users allowed in a space) value and
     * update state(setSpaceLines) to set max capacity of this particular space.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop Field name of the clicked input box
     * @return {Function}
     */
    const handleChange = (prop) => (event) => {
        if (event.target.value <= Constants.space.MAX_CAPACITY) {
            setSpaceLines({...spaceLines, [prop]: event.target.value});
            spaceData && spaceData.map((v, i) => {
                if (v.space_uuid === spaceLines.spaceId) {
                    if (prop === "max_capacity") {
                        spaceData[i].max_capacity = event.target.value;
                    }
                }
            });
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Function will take value(from parameter) for the space to decide either it is a VIP space or a
     * normal space and set it into a state(setVip, setSpaceLines).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e  Javascript event object
     */
    const handleVipType = (e) => {
        setVip(e.target.value);
        setSpaceLines({...spaceLines, isVip: e.target.value})
        spaceData && spaceData.map((v, i) => {
            if (v.space_uuid === spaceLines.spaceId) {

                spaceData[i].is_vip_space = e.target.value

            }
        })
    }

    useEffect(() => {
        setEventUuid(props.eventId);
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when 'onUpdate'(function to make an API call to update space's data)
     * function will call an API successfully and get data(all space related information) from its parameter and update
     * all states(setFirstData, setFirstSelectedHost, setSpaceLines, setVip, setHostName, setSpaceHostId) related to
     * space data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Space} data Data of a particular space
     */
    const handleShowSpace = (data) => {
        console.log('data', data)
        if (eventStatus.is_live && eventStatus.is_live == 1) {
            //     alert.show("Event is currently live can not edit the spaces ", { type: 'error' });
            setDisabled(true)
        }
        setFirstData(
            {
                // ...spaceLines,
                space_name: data.space_name ? data.space_name : "",
                space_short_name: data.space_short_name ? data.space_short_name : '',
                max_capacity: data.max_capacity ? data.max_capacity : "",
                spaceId: data.space_uuid ? data.space_uuid : "",
                vip: data.is_vip_space ? data.is_vip_space : ''
            })

        if (firstSelectedHost && firstSelectedHost != data.space_uuid) {
            spaceData && spaceData.map((v, i) => {
                if (v.space_uuid === firstSelectedHost) {
                    spaceData[i].max_capacity = firstData.max_capacity;
                    spaceData[i].space_name = firstData.space_name;
                    spaceData[i].space_short_name = firstData.space_short_name;
                    spaceData[i].is_vip_space = firstData.vip;
                }
            })
            setFirstSelectedHost(data.space_uuid)
        } else {
            setFirstSelectedHost(data.space_uuid)
        }
        setSpaceLines(
            {
                // ...spaceLines,
                space_name: data.space_name ? data.space_name : "",
                space_short_name: data.space_short_name ? data.space_short_name : '',
                max_capacity: data.max_capacity ? data.max_capacity : "",
                spaceId: data.space_uuid ? data.space_uuid : "",
                isVip: data.is_vip_space ? data.is_vip_space : 0
                // eventState: data.event_state ? data.event_state : {}
            })
        setVip(data.is_vip_space ? data.is_vip_space : 0)
        setHostName(!_.isEmpty(data.space_hosts) ? data.space_hosts : [])
        setSpaceHostId(!_.isEmpty(data.space_hosts) ? data.space_hosts.id : '')
        // }
        initialize(data);
        setShowSpaceLine(true)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Function will take value and name(from parameter) of the header line components and then update
     * their values associate with the space in a state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop Name of field which is clicked by user
     * @returns {function}
     */
    const handleSpaceLine = (prop) => (event) => {

        setSpaceLines({...spaceLines, [prop]: event.target.value});

        spaceData &&
        spaceData.map((v, i) => {
            if (v.space_uuid === spaceLines.spaceId) {
                if (prop === "space_name") {
                    spaceData[i].space_name = event.target.value;
                } else if (prop === "space_short_name") {
                    spaceData[i].space_short_name = event.target.value;
                }
            }
        });
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will make an API call to update a space/add a new space.This will prepare form data
     * input by the user and call 'updateSpaces' API. Once the API called successfully then it will update all
     * the states(related to space information) from API's response data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onUpdate = () => {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("max_capacity", parseInt(spaceLines.max_capacity));
        formData.append("space_name", spaceLines.space_name);
        formData.append("space_short_name", spaceLines.space_short_name);
        formData.append("space_uuid", spaceLines.spaceId);
        formData.append("hosts[]", spaceHostId);
        formData.append("space_type", vip);
        formData.append("is_mono", props.venueType == MONO_SPACE ? 1 : 0);

        if (spaceHostId !== undefined) {
            try {
                props.updateSpaces(formData).then((res) => {
                    const data = res.data.data
                    spaceData && spaceData.map((v, i) => {
                        if (v.space_uuid === data.space_uuid) {
                            spaceData[i] = data
                        }
                    })
                    handleShowSpace(res.data.data)
                    alert.show(t("notification:rec add 1"), {type: "success"})
                }).catch((err) => {
                    if (err && _.has(err.response.data, ['errors'])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: 'error'});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data;
                        for (let key in er) {
                            alert.show(er[key], {type: 'error'});
                        }
                    } else {
                        alert.show(Helper.handleError(err), {type: 'error'});
                    }
                })
            } catch (err) {
                alert.show(Helper.handleError(err), {type: 'error'});
            }
        } else {
            alert.show(t("req"), {type: 'error'});
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will open a popup component and take confirmation to perform delete
     * space action. That popup component contains 2 button('Yes', 'No'). If user click on 'Yes' then it will
     * pass space's data(which need to be delete) to 'handleDelete' function otherwise it will
     * close the popup if user clicks on 'Cancel' button.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Uuid of space which needs to be delete
     */
    const confirmDelete = (id) => {
        confirmAlert({
            message: `${t("confirm:sure")}`,
            confirmLabel: t("confirm:confirm"),
            cancelLabel: t("confirm:cancel"),
            buttons: [
                {
                    label: t("confirm:yes"),
                    onClick: () => {
                        handleDelete(id);
                    },
                },
                {
                    label: t("confirm:no"),
                    onClick: () => {
                        return null;
                    },
                },
            ],
        });
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user adding a new space details in the form(opens when click on add
     * new space button from space slider component) and click on 'close' button(just below the form) and it will
     * close the form.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const hideSpaceForm = () => {
        setShowSpaceLine(false);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to delete a space and once the call is successfully completed
     * then it will  filter data and update states(setSpaceData) to show instant reflection.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Space uuid which needs to be delete
     */
    const handleDelete = (id) => {
        const data = {
            _method: "DELETE",
            space_uuid: id,
        };
        try {
            props
                .updateSpaces(data)
                .then((res) => {
                    var list = props.multiSpaces.filter((e, i) => {
                        return props.multiSpaces[i].space_uuid != id;
                    });
                    props.removeSpace(id);
                    setSpaceData(list);
                    alert.show(t("notification:rec add 1"), {type: "success"});

                    // props.getData(eventUuid)
                    setShowSpaceLine(false);
                })
                .catch((err) => {
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data;
                        if (er.length > 1) {
                            for (let key in er) {
                                alert.show(er[key], {type: "error"});
                            }
                        } else {
                            alert.show(err.response.data.msg, {type: "error"});
                        }
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});

        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to create a space.This will take all necessary data(space
     * name, header lines, max capacity, vip space status) from a state('spaceLines')
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onCreate = () => {
        const formData = new FormData();
        formData.append("_method", "POST");

        formData.append("space_name", spaceLines.space_name);
        formData.append("space_short_name", spaceLines.space_short_name);
        formData.append("space_mood", "space mood");
        formData.append("space_type", vip);
        formData.append("max_capacity", parseInt(spaceLines.max_capacity));
        formData.append("hosts[]", spaceHostId);
        formData.append("is_vip_space", vip);
        formData.append("event_uuid", props.eventId);
        if (spaceHostId && spaceHostId !== undefined) {
            try {
                props
                    .updateSpaces(formData)
                    .then((res) => {
                        const data = res.data.data;
                        const list = spaceData;

                        props.addSpace(data);

                        list.push(data);
                        // props.multiSpaces.push(data)
                        setSpaceData(list);
                        setSpaceLines({
                            space_name: "",
                            space_short_name: "",
                            spaceHost: "",
                            max_capacity: "",
                            spaceId: "",
                            eventState: "",
                            isVip: 0,
                        });
                        setShowSpaceLine(false);
                        alert.show(t("notification:rec add 1"), {type: "success"});
                    }).catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
            } catch (err) {
                if (err && _.has(err.response.data, ["errors"])) {
                    var errors = err.response.data.errors;
                    for (let key in errors) {
                        alert.show(errors[key], {type: "error"});
                    }
                } else if (err && _.has(err.response.data, ["msg"])) {
                    var er = err.response.data.msg;
                    for (let key in er) {
                        alert.show(er[key], {type: "error"});
                    }
                    // alert.show(err.response.data.msg,{type:'error'});

                } else {
                    alert.show(Helper.handleError(err), {type: "error"});
                }
            }
        } else {
            alert.show(t("req"), {type: "error"});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when venue type component render(after add new space, modify space
     * settings) and return true if added space will exist in all space other wise return false to update disable
     * condition for venue type radio buttons.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {Boolean}
     */
    const checkVip = () => {
        const firstSpace = spaceData && spaceData[0]
        if (_.has(firstSpace, ["space_uuid"]) && firstSpace.space_uuid == spaceLines.spaceId) {
            return true;
        }
        return false;
    }

    return (
        <div>
            {/* {showSpaceLine && eventStatus.is_live != 1 && <div> */}
            {showSpaceLine && <div>
                <form onSubmit={handleSubmit}>
                    {props.venueType !== MONO_SPACE &&
                    <Grid container spacing={3}>
                        <Grid item lg={3}>
                            <p>{t("Space line 1")}</p>
                        </Grid>
                        <Grid item lg={4}>
                            <Field
                                name="space_name"
                                disabled={disabled}
                                placeholder={t("Space Line 1")}
                                variant="outlined"
                                className="ThemeInputTag"
                                // value={spaceLines.spaceLine1}
                                component={renderTextField}
                                onChange={handleSpaceLine("space_name")}
                                inputProps={{
                                    value: spaceLines.space_name,
                                    maxLength: 14
                                }}
                            />
                        </Grid>
                    </Grid>
                    }

                    {props.venueType !== MONO_SPACE &&
                    <Grid container spacing={3}>
                        <Grid item lg={3}>
                            <p>{t("Space Line 2")}</p>
                        </Grid>
                        <Grid item lg={4}>


                            <Field
                                name="space_short_name"
                                disabled={disabled}
                                placeholder={t("Space Line 2")}
                                variant="outlined"
                                className="ThemeInputTag"
                                // value={spaceLines.space_short_name}
                                component={renderTextField}
                                onChange={handleSpaceLine("space_short_name")}
                                inputProps={{
                                    value: spaceLines.space_short_name,
                                    maxLength: 14
                                }}

                            />
                        </Grid>
                    </Grid>
                    }
                    {props.venueType !== MONO_SPACE &&

                    <Grid container spacing={3}>
                        <Grid item lg={3}>
                            <p className="VeneuTypeTxt">
                                {props.eventRolelabelCustomized === 1
                                    ? Helper.getLabel("vip", props.eventRoleLabels)
                                    : t("labels:VIP")
                                } Space:
                            </p>
                        </Grid>
                        <Grid item>
                            <FormControl component="fieldset">
                                <RadioGroup
                                    value={`${vip}`}
                                    onChange={handleVipType}
                                    className="VenueTypeRadioBtn"
                                    aria-label="gender"
                                    name="gender1"
                                >
                                    <FormControlLabel value="1" control={<Radio />} label={t("Yes")}
                                                      disabled={disabled == true ? true : checkVip()} />
                                    <FormControlLabel value="0" control={<Radio />} label={t("No")}
                                                      disabled={disabled == true ? true : checkVip()} />
                                </RadioGroup>
                            </FormControl>
                        </Grid>
                    </Grid>
                    }

                    <Grid container spacing={3}>
                        <Grid item lg={3}>
                            <p>{t("max")}</p>
                        </Grid>
                        <Grid item lg={4}>

                            <Field

                                name="max_capacity"
                                id="max_capacity"
                                placeholder={t("cap")}
                                variant="outlined"
                                className="ThemeInputTag"
                                component={renderNumberField}
                                onChange={handleChange("max_capacity")}
                                // value={spaceData.max_capacity}
                                // type="number"
                                inputProps={{min: 12, max: Constants.space.MAX_CAPACITY}}
                                disabled={disabled}

                            />
                        </Grid>
                    </Grid>

                    <Grid container spacing={3}>
                        <Grid item lg={3}>
                            {/* <p>{t("spaceHost")}</p> */}
                            <p>{props.eventRolelabelCustomized == 1 ? Helper.getLabel("space_host", props.eventRoleLabels) : t("labels:SpaceHost")}</p>
                        </Grid>
                        <Grid item lg={4} className="spaceHostDropDown">

                            <UserAutocomplete id={(id) => setSpaceHostId(id)} name={hostName} disabled={disabled} />
                        </Grid>

                    </Grid>
                    {_.has(eventStatus, ["is_live"]) && eventStatus.is_live != 1 &&
                    <Button variant="contained" className="modify_add_btn" color="primary" type="submit"
                            onClick={spaceLines.spaceId ? handleSubmit(onUpdate) : handleSubmit(onCreate)}>{spaceLines.spaceId ? "Modify" : "Add"}</Button>
                    }
                    <Button variant="contained" className="modify_add_btn" color="primary" type="submit"
                            onClick={hideSpaceForm}>Close</Button>

                    {/* <Button variant="contained"  className="modify_add_btn" color="primary" type="submit" onClick={spaceLines.spaceId ? handleSubmit(onUpdate) : handleSubmit(onCreate)}>{spaceLines.spaceId ? "Modify" : "Add"}</Button> */}

                </form>
            </div>}
            <div>
                <SliderSPersons
                    vip={vip}
                    spaceLines={spaceLines}
                    showSpaceLine={handleShowSpace}
                    spaceData={spaceData}
                    deleteSpace={(id) => confirmDelete(id)}
                    selectedItem={spaceLines.spaceId}
                    allowAdd={props.allowAdd}
                    venueType={props.venueType}
                />
            </div>

        </div>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        getSpaces: (data) => dispatch(eventAction.getSpaces(data)),
        updateSpaces: (data) => dispatch(eventAction.updateSpaces(data)),
        spaceHostSearch: (data) => dispatch(eventAction.spaceHostSearch(data)),
    };
};

const mapStateToProps = (state) => {
    return {
        event_data: state.Auth.eventDetailsData,
        eventRoleLabels: state.Auth.eventRoleLabels.labels,
        eventRolelabelCustomized: state.Auth.eventRoleLabels.label_customized,
    };
};
MultiSpaces = reduxForm({
    form: "multispaceform", // a unique identifier for this form
    validate,
    keepDirtyOnReinitialize: true,
})(MultiSpaces);

export default connect(mapStateToProps, mapDispatchToProps)(MultiSpaces);
