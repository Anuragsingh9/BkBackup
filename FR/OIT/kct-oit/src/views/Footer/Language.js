import React, {useState, useRef, useEffect} from "react";
import Helper from "../../Helper";
import userAction from "../../redux/action/apiAction/user";
import userReduxAction from "../../redux/action/reduxAction/user";

import {connect} from "react-redux";
import {reactLocalStorage} from "reactjs-localstorage";
import _ from "lodash";
import {useTranslation} from "react-i18next";
import MenuItem from "@material-ui/core/MenuItem";
import FormControl from "@material-ui/core/FormControl";
import InputLabel from "@material-ui/core/InputLabel";
import Select from "@material-ui/core/Select";
import "./Footer.css";
import {useAlert} from "react-alert";
import TranslateIcon from '@mui/icons-material/Translate';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component manage Language changes and implement in whole project with the help of react-i18next
 * package and send the data on server by fetching language api
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contains language related functions required in footer component
 * @param {Function} props.getUserData Function to get current user data.
 * @param {Function} props.setLanguage Function to set current language in footer
 * @param {Function} props.updateLang Function to update current selected language
 * @param {UserDataObject} props.userSelfData All data related to the self user
 * @returns {JSX.Element}
 */

const Language = (props) => {
    // i18n hook for localization
    const {t, i18n} = useTranslation(["footer"]);
    const alert = useAlert();
    const [activeLang, setActiveLang] = useState(
        reactLocalStorage.get("current_lang")
    );
    const alertRef = useRef(null);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will render a MUI select component to select language.
     * <br>
     * MUI - Material UI Library
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
     */
    const renderLanguage = () => {
        let language = ["EN", "FR"];

        return (
            <FormControl className="languageSelectorWrap">
                {/* <InputLabel shrink id="demo-simple-select-placeholder-label-label">
                    {t("Language")}
                </InputLabel> */}
                <TranslateIcon />
                <Select
                    className="lan-select-input"
                    labelId="demo-simple-select-placeholder-label-label"
                    id="demo-simple-select-placeholder-label"
                    value={activeLang}
                    MenuProps={{
                        anchorOrigin: {
                            vertical: "top",
                        },
                        getContentAnchorEl: null,
                    }}
                    onChange={selectLang}
                >

                    {language.map((v, i) => {
                        return (
                            <MenuItem value={v} className={"active cursor"}>
                                {v}{" "}
                            </MenuItem>
                        );
                    })}
                </Select>
            </FormControl>
        );
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function prepares data for setLanguage and according to the API response it set the active
     * language for the user.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event JavaScript event object
     */
    const selectLang = (event) => {
        const lang = event.target.value;
        const correctLang = lang.toLowerCase();
        const kct_enabled = reactLocalStorage.get("kct_enabled");
        const oitToken = reactLocalStorage.get("oitToken");
        const id = reactLocalStorage.get("userId");

        if (kct_enabled === 0 || !oitToken) {
            setActiveLang(lang);
            reactLocalStorage.set("current_lang", lang);
            return i18n.changeLanguage(correctLang);
            // window.location.reload();
            // return;
        }
        const formData = new FormData();
        formData.append("_method", "POST");
        formData.append("user_id", id);
        formData.append("field", "lang");
        formData.append("value", correctLang);
        try {
            props
                .setLanguage(formData)
                .then((res) => {
                    setActiveLang(lang);
                    props.updateLang(lang);
                    reactLocalStorage.set("current_lang", lang);
                    return i18n.changeLanguage(correctLang);
                    window.location.reload();
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will save the current selected language in the local storage.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Current loggedIn user's ID
     */
    const getProfileData = (id) => {
        const {userSelfData} = props;
        if (_.has(userSelfData, ["setting", "lang"])) {
            reactLocalStorage.set("current_lang", userSelfData.setting.lang);
        }
    };


    useEffect(() => {
        const id = reactLocalStorage.get("userId");
        getProfileData(id);
        const lang = reactLocalStorage.get("current_lang");
        props.updateLang(lang);
        if (lang) {
            i18n.changeLanguage(lang);
            setActiveLang(lang.toUpperCase());
        } else {
            setActiveLang("EN");
        }
    }, []);
    return renderLanguage();
};

const mapDispatchToProps = (dispatch) => {
    return {
        getUserData: (id) => dispatch(userAction.getUserData(id)),
        setLanguage: (data) => dispatch(userAction.setLanguage(data)),
        updateLang: (data) => dispatch(userReduxAction.setLang(data))
    }
}

const mapStateToProps = (state) => {
    return {
        userSelfData: state.Auth.userSelfData
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(Language);
