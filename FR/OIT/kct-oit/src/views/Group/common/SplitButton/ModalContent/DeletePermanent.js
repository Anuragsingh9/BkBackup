import React, {useState} from "react";
import {TextField, Button} from "@mui/material";
import {Field, reduxForm} from "redux-form";
import {useTranslation} from "react-i18next";
import i18next from "i18next";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Method to validate(match - pattern and length) entered email & password by user using regex pattern.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values object that contains email and password values.
 * @param {String} values.email Email address
 * @param {String} values.password Password
 */
const validate = (values) => {
    const errors = {};
    var emailRegex =
        /^$|^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    const requiredFields = ["email"];
    requiredFields.forEach((field) => {
        if (!values[field]) {
            errors[field] = `${i18next.t("auth:required")}`;
        }
    });
    if (values["email"] && !emailRegex.test(values["email"])) {
        //if entered email not matched regex pattern
        errors["email"] = `${i18next.t("auth:email invalid")}`;
    }
    return errors;
};


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common structure for a input field to render input box for youtube link & vimeo link. This
 * will take data(from parameter where it called) which is necessary to render relative text fields.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Boolean} invalid Enter value is invalid
 * @param {Boolean} touched To indicate if the input box is touched or not
 * @param {String} error Error message from input box
 * @param {String} custom Custom number of input box
 * @returns {JSX.Element}
 */
const renderTextField = ({
                             input,
                             value,
                             label,
                             defaultValue,
                             meta: {invalid, touched, error},
                             ...custom
                         }) => {
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
 * @class
 * @component
 *
 * -------------------------------------------------------------------------------------------------------------------
 * @description Child component for delete permanent action.
 * -------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contains all information to show in split button component.
 * @param {Function} props.deleteGroup Function to delete a group.
 * @param {Function} props.group_key Unique key for the specific group.
 * @param {Function} props.handleCloseModal Function to close delete group modal box.
 * @param {Function} props.reloadGroup Function to reload data as per pagination.
 * @returns {JSX.Element}
 */
const DeletePermanent = (props) => {
    const [email, setEmail] = useState("");
    const {handleSubmit, pristine, reset, submitting, initialize, accessMode} = props;

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method handles email change value and update the state
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e JavaScript event object.
     */
    const handleChange = (e) => {
        setEmail(e.target.value);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method send data for permanent delete group
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onSubmit = () => {
        const data = {
            _method: "DELETE",
            group_key: props.group_key,
            delete_mode: 2,
            confirmation_email: email,
        };
        console.log("sending delete data", data);
        props.deleteGroup(data);
        props.handleCloseModal();
    };

    return (
        <div className="modalContent--reusable">
            <h2>Confirm Email!</h2>
            <p>Enter Pilot's email address to delete group permanently.</p>
            {/* <TextField
        placeholder="Enter Email"
        size="small"
        style={{ width: "80%", "margin-bottom": "18px" }}
      ></TextField> */}
            <form onSubmit={handleSubmit(onSubmit)}>
                <div>
                    <Field
                        name="email"
                        placeholder="Enter Email"
                        onChange={handleChange}
                        size="small"
                        variant="outlined"
                        component={renderTextField}
                        value={email}
                    />
                </div>

                <div className="modalFooter--reusable">
                    <Button color="primary" variant="contained" type="submit">
                        Delete Permanently
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default reduxForm({
    form: "emailForm", // unique key for form
    validate,
    keepDirtyOnReinitialize: true,
})(DeletePermanent);
