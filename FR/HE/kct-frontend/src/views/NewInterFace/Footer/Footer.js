import React, {useEffect} from 'react';
import './Footer.css';
import Language from './Language.js';
import SvgIconFooter from './SvgIconFooter.js';
import {useTranslation} from 'react-i18next';
import newInterfaceActions from "../../../redux/actions/newInterfaceAction";
import {connect} from "react-redux";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This is a common component for rendering/displaying footer on every page that contains localization
 * feature and terms and condition information and Copyright information.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const Footer = (props) => {
    const {t, i18n} = useTranslation(['footer', 'SimpleLogin']);


    useEffect(() => {
        props.setLangChange(i18n.language);

    }, [i18n.language])

    const {graphics_data} = props;
    return (
        <div className="footer-bg text-center">

            <div className="container">
                <ul className="col-sm-5 col-md-5 col-lg-6 footerUlList footer-ul list-inline mb-0 mt-20 inline-block 
                pull-left">
                    <Language />
                    <li><a href="#">{t("terms of uses")}</a></li>
                    <li><a href="#">{t("Privacy Policy")}</a></li>
                </ul>
                <span className="col-sm-3 footerCopyright d-inline-block mt-20  copyright-txt">Copyright HumannConnect ©
                    {new Date().getFullYear()} </span>
                <div className="col-sm-3 p-0 footer-powerby list-inline  mb-0 inline-block pull-right">
                    <span class="">powered by</span>
                    <SvgIconFooter graphics_data={graphics_data} />
                    <span class="">Just as in real life</span>
                </div>
            </div>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        setLangChange: (data) => dispatch(newInterfaceActions.NewInterFace.setLangChange(data)),
    }
}
const mapStateToProps = (state) => {
    return {
        langChange: state.NewInterface.langChange,
    }
}

export default connect(mapStateToProps,mapDispatchToProps)(Footer);