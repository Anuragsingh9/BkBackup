import React, {useEffect, useRef, useState} from "react";
import Helper from "../../../Helper";
import authActions from "../../../redux/actions/authActions";
import {connect} from "react-redux";
import {reactLocalStorage} from 'reactjs-localstorage';
import {useTranslation} from 'react-i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This component is a child component which is used to handle localization for "Events platform" it
 * manages current language and can be modify it contains two types of languages English and French and updates
 * on server is language is changed.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.changeLanguage To change the language
 * @returns {JSX.Element}
 * @constructor
 */
const Footer = (props) => {
    const {t, i18n} = useTranslation(['footer', 'SimpleLogin']);

    //default language is French
    const [active_lang, setActiveLang] = useState("FR")
    const alertRef = useRef(null);

    useEffect(() => {
        const lang = reactLocalStorage.get("current_lang");
        if (lang) {
            setActiveLang(lang);
        } else {
            setActiveLang("EN");
        }
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle language selection . Upon clicking on the language option it sets
     * that language as active language and change font style to bold and update state for that value and returns HTML
     * list item.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element|unknown[]}
     */
    const renderLanguage = () => {
        let language = ["EN", "FR"];

        if (language) {
            return Object.values(language).map((v, i) => {
                if (language.length == 1) {
                    return <li data-lang={v} className={'active cursor'} onClick={selectLang}>{v} </li>
                }
                if (i != 0) {
                    return <li data-lang={v} className={(active_lang == v) ? 'active cursor' : 'cursor'}>
                        <span onClick={selectLang} data-lang={v}>{v}</span></li>
                }
                return <li data-lang={v} className={(active_lang == v) ? 'active cursor' : 'cursor'}
                           onClick={selectLang}>
                    {v}
                </li>
            })
        } else {
            return <li data-lang={"FR"} className={'active cursor'} onClick={selectLang}>FR</li>
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to upload new selected language value on server by using API call and on
     * getting successful response it updates state of latest value and also update that value in localstorage
     * current_lang key for use on other page as well.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {*} event
     * @returns {Promise<Function>}
     */
    const selectLang = (event) => {
        const lang = event.target.getAttribute('data-lang');
        const correctLang = lang.toLowerCase();
        const kct_enabled = reactLocalStorage.get("kct_enabled");
        const accessToken = reactLocalStorage.get("accessToken");

        if (kct_enabled == 0 || !accessToken) {
            setActiveLang(lang);
            reactLocalStorage.set("current_lang", lang.toUpperCase());
            return i18n.changeLanguage(correctLang);

        }
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("lang", lang);
        try {
            props.changeLanguage(formData)
                .then((res) => {
                    setActiveLang(lang);
                    reactLocalStorage.set("current_lang", lang.toUpperCase());
                    return i18n.changeLanguage(correctLang);
                })
                .catch((err) => {
                    alertRef.show(Helper.handleError(err), {type: "error"});
                })
        } catch (err) {
            alertRef.show(Helper.handleError(err), {type: "error"});
        }
    }


    return renderLanguage()
};

const mapStateToProps = (state) => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {
        changeLanguage: (data) => dispatch(authActions.Auth.changeLanguage(data)),
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Footer);


