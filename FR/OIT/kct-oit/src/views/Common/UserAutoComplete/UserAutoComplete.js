import React, {useEffect, useState} from "react";
import TextField from "@material-ui/core/TextField";
import Autocomplete from "@material-ui/lab/Autocomplete";
import Helper from "../../../Helper";
import {connect} from "react-redux";
import userAction from "../../../redux/action/apiAction/user";
import {useAlert} from "react-alert";
import _ from "lodash";
import "./UserAutoComplete.css";
import {useParams} from "react-router-dom";

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component used for searching and fetching users data by their email. It fetches the
 * data according to the key entered by user.
 * <br>
 * <br>
 * The search API triggers only when the entered key is greater than 2 characters.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Boolean} props.disabled Auto complete disabled or not
 * @param {Event} props.event_data Event Data
 * @param {Object} props.name Space host name
 * @param {Function} props.userSearch Searching on event data
 * @returns {JSX.Element}
 */
const UserAutocomplete = (props) => {
    const [val, setVal] = useState("");
    const [selectUser, setSelectUser] = useState([]);
    const alert = useAlert();
    const {gKey} = useParams();


    useEffect(() => {
        if (!_.isEmpty(props.name)) {
            const spaceHost = props.name;
            setVal({
                id: spaceHost.id ? spaceHost.id : "",
                fname: spaceHost.fname ? spaceHost.fname : "",
                lname: spaceHost.lname ? spaceHost.lname : "",
                email: spaceHost.email ? spaceHost.email : "",
            });
            if (props.setSelectedData) {
                props?.setSelectedData({
                    ...props.selectedData,
                    pilot: {
                        fname: spaceHost.fname ? spaceHost.fname : "",
                        lname: spaceHost.lname ? spaceHost.lname : "",
                        email: spaceHost.email ? spaceHost.email : "",
                    }
                })
            }
        } else {
            setVal({
                id: "",
                fname: "",
                lname: "",
                email: "",
            });
        }
    }, [props.name]);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This is used for handle changing data
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     * @param {Object} value Object of user
     */
    const handleChange = (e, value) => {
        selectChange(value);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This fetch api and get data of enter entity when entity length is 3 or greater than 3
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Entity data
     */
    const selectChange = (data) => {
        if (data && data.length >= 3) {
            let params = {};

            if (props.searchUser) {
                params = {
                    key: data,
                    search: ["fname", "lname", "email"],
                };
            } else {
                params = {
                    key: data,
                    event_uuid: props.event_data.event_uuid,
                    mode: "simple",
                    filter: "space_host",
                    groupKey: gKey,
                };
            }

            try {
                props
                    .userSearch(params)
                    .then((res) => {
                        if (props.setSelectedData) {
                            let preSelectedUser = props.selectedData.copilot;
                            if (preSelectedUser) {
                                const filteredData = res.data.data.filter(ad =>
                                    preSelectedUser.every(fd => fd.email !== ad.email));
                                setSelectUser(filteredData);
                            } else {
                                setSelectUser(res.data.data);
                            }
                        } else {
                            setSelectUser(res.data.data);
                        }
                    })
                    .catch((err) => {
                        alert.show(Helper.handleError(err), {type: "error"});
                    });
            } catch (err) {
                alert.show(Helper.handleError(err), {type: "error"});
            }
        } else {
            setSelectUser([]);
        }
    };
    return (
        <Autocomplete
            // style={{ width: 300 }}
            options={selectUser ? selectUser : ""}
            classes={{
                option: props.classes,
            }}
            name={props.name}
            value={val}
            autoHighlight
            getOptionLabel={(option) =>
                option.fname
                    ? `${option.fname} ${option.lname} ${"("}${option.email}${")"} `
                    : ""
            }
            onInputChange={handleChange}
            onChange={(event, values) => {
                if (values) {
                    // selectChange(values)
                    props.id(values.id);
                    setVal({
                        fname: values.fname,
                        lname: values.lname,
                        email: values.email,
                    });
                    if (props.setSelectedData) {
                        props.setSelectedData({
                            ...props.selectedData,
                            pilot: {
                                fname: values.fname,
                                lname: values.lname,
                                email: values.email,
                            }
                        })
                    }
                }
            }}
            renderOption={(option) => (
                <React.Fragment>
                    {option.fname} {option.lname} ({option.email})
                </React.Fragment>
            )}
            renderInput={(params) => (
                <TextField
                    {...params}
                    // label="Space Host "
                    className="autoCompleteInput"
                    variant="outlined"
                    size="small"
                    disabled={props.disabled ? props.disabled : false}
                    errorText={props.errorText}
                    error={props.error}
                    inputProps={{
                        ...params.inputProps,
                        autoComplete: "new-password", // disable autocomplete and autofill
                    }}
                />
            )}
        />
    );
};

const mapDispatchToProps = (dispatch) => {
    return {
        userSearch: (data) => dispatch(userAction.userSearch(data)),
    };
};

const mapStateToProps = (state) => {
    return {
        event_data: state.Auth.eventDetailsData,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(UserAutocomplete);
