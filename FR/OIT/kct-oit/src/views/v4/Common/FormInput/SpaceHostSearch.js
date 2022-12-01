import React from "react";
// import "./UserAutoComplete.css";
import AutoCompleteInput from "./AutoCompleteInput";
import Helper from "../../../../Helper";
import userAction from "../../../../redux/action/apiAction/user";
import {connect} from "react-redux";
import {Field} from "redux-form";
import {useAlert} from "react-alert";

const renderAutoComplete = (
    {
        input,
        placeholder,
        onTypeSearchHandler,
        label,
        defaultValue,
        meta: {touched, error},
        ...custom
    },
) => {
    return (
        <React.Fragment>
            <AutoCompleteInput
                name={input.name}
                value={input.value}
                onTypeSearchHandler={onTypeSearchHandler}
                onChange={input.onChange}
                disabled={false}
                size="small"
                className="full_width"
                placeholder={placeholder}
                variant="filled"
                errorText={(touched && error) || custom.isValidate}
                {...input}
                {...custom}
                error={(touched && error) || custom.isValidate}
                helperText={touched && error}
            />
            {/*{touched && error && <span className={'text-danger'}>{error}</span>}*/}
        </React.Fragment>
    );
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To provide the component with auto select for space host search
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param props
 * @returns {JSX.Element}
 * @constructor
 * @class
 * @component
 */
let SpaceHostSearch = (props) => {

    const alert = useAlert();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the options when user type the word with minimum 2 length
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param searchKey
     * @param successCallback
     */
    const onTypeSearchHandler = (searchKey, successCallback) => {
        if (searchKey && searchKey.length >= 2) {
            let params = {
                key: searchKey,
                search: ["fname", "lname", "email"],
            };
            props.userSearch(params)
                .then((res) => {
                    successCallback(res.data.data);
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } else {
            successCallback([]);
        }
    };


    return (
        <Field
            name={props.name}
            disabled={props.disabled}
            placeholder={props.placeholder}
            variant="filled"
            inputVariant="filled"
            className="ThemeInputTag"
            validate={props.validate}
            component={renderAutoComplete}
            onTypeSearchHandler={onTypeSearchHandler}
            isValidate={props.isValidate}
        />
    );
};

const mapDispatchToProps = (dispatch) => {
    return {
        userSearch: (data) => dispatch(userAction.userSearch(data)),
    };
};

SpaceHostSearch = connect(null, mapDispatchToProps)(SpaceHostSearch);

export default SpaceHostSearch;
