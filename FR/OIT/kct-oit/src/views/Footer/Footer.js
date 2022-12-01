import React from 'react';
import './Footer.css';
import Language from './Language.js';
import {useTranslation} from 'react-i18next';
import Container from '@material-ui/core/Container';
import Grid from '@material-ui/core/Grid';
import CustomContainer from '../Common/CustomContainer/CustomContainer';
/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a footer main component which is including language selector dropdown component(to change
 * language) and external links for Terms of uses and privacy policies with copyright information.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @returns {JSX.Element}
 * @constructor
 */
const Footer = (props) => {
    const [t, i18n] = useTranslation('footer');

    return (
        <div className="new-interface-footer">
            <>
                <Grid container style={{"width":"100%"}}>
                    <Grid item className="footerDiv-1" lg={3} xs={3}>
                        <Language />
                    </Grid>
                    <Grid item className="footerDiv-2" lg={6} xs={6}>
                        <ul>
                            <li><a href="#" className="header-text-color">{t("terms of uses")}</a></li>
                            <li><a href="#" className="header-text-color">{t("Privacy Policy")}</a></li>
                        </ul>
                    </Grid>
                    <Grid item className="footerDiv-3" lg={3} xs={3}>
                        <span
                            className="col-sm-4 footerCopyright d-inline-block mt-20 header-text-color copyright-txt"
                        >
                            Copyright humannConnect © {new Date().getFullYear()}
                        </span>
                    </Grid>
                </Grid>
            </>
        </div>
    )
}

export default Footer;