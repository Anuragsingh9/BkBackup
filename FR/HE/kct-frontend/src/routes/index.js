import React, {Fragment} from "react";
import {Link, Route, Routes, Navigate} from "react-router-dom";
import Helper from '../Helper';
import MyEventList from "../views/MyEventList/MyEventList";
import AuthLayout from "../views/NewInterFaceRegister/AuthLayout/AuthLayout3.js";
import withContext from "../withContext";
import ForgetPassword from "../views/ForgetPassword/ForgetPassword";
import ResetPassword from "../views/ForgetPassword/ResetPassword";
import MagicLogin from "../views/Authenthication/MagicLink/MagicLogin";
import KeepContactagent from "../agents/KeepContactagent";
import {reactLocalStorage} from 'reactjs-localstorage';
import _ from 'lodash';
import CSSGenerator from '../views/DynamicCss.js'
import {KCTLocales} from '../localization';
import {KeepContact as KCT} from '../redux/types';
import {connect} from "react-redux";
import newInterfaceActions from '../redux/actions/newInterfaceAction';
import DashboardContainer from '../views/DashboardContainer/DashboardContainer.js';
import NewRegistration from '../views/NewInterFaceRegister/Registration/Registration';
import NewLogin from '../views/NewInterFaceRegister/Login/Login';
import OtpProcess2 from '../views/NewInterFaceRegister/OTPVerification/OtpProcess';
import SimpleAuthRoute from './SimpleAuthRoute.js';
import {Provider as AlertContainer} from 'react-alert'
import UserInfoUpdate from "../views/Authenthication/EventInfo/UserInfoUpdate";
import SimpleLogin from '../views/NewInterFaceRegister/SimpleLogin/SimpleLogin.js';
import ChangePassword from '../views/ChangePassword/ChangePassword'
import PrivateRoute from "./privateRoute";
import PageExpired from "../views/Authenthication/PageExpired/PageExpired";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for declaring the routes of the different components. Here all the routes are list for front
 * application and it will render the respective component depends on route and data
 *
 * <br>Here before loading the routes some data related to application graphics is being fetched for basic ui
 * presentation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @class
 * @component
 */
class AppRoutes extends React.Component {

    /**
     * @param {Object} props Props passed from parent component
     * @param {Function} props.updateInit To fetch and store the init data for the application basic task
     * @param {Function} props.setInterfaceGraphics To set the graphics data in redux store
     * @param {Function} props.setTestAudioUrl To set the test audio url in redux for media device popup
     */
    constructor(props) {
        super(props);
        this.state = {
            kctEnabled: true,
            dataLoad: false,
            videoLink: ''
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Component lifecycle that make sures get the organisation data,
     * and hide zoom sdk div by default created by websdk package.
     * -----------------------------------------------------------------------------------------------------------------
     **/
    componentDidMount() {
        var a = window.location.hostname.split('.')[0];

        document.title = `HumannConnect event For ${a}`;

        if (reactLocalStorage.get('accessToken')) {
            this.getOrganisationWithToken();
            // this.getEventGraphicData()
        } else {
            this.getOrganisation();
        }

        this.hideZoom();
        this.setGlobalRef();
        const queryString = window.location.href

        const urlParams = new URLSearchParams();

        const event_uuid = urlParams.get('event_uuid')
        if (queryString) {
            console.log("urlParams", this.props);
        }


    }

    getEventGraphicData = () => {

        try {
            return KeepContactagent.Event.getEventGraphicData().then((res) => {

                this.props.setEventDesignSetting(res.data.data);

            }).catch((err) => {
                console.error( "event graphics fetch err", err)

        })
        } catch (err) {
            console.error( "event graphics fetch err",err)
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This hides zoom sdk div by default created by web sdk package.
     * and makes the root id element visible and scrollable.
     * -----------------------------------------------------------------------------------------------------------------
     */
    hideZoom = () => {
        const zoomTag = document.getElementById('zmmtg-root');
        if (zoomTag) {
            zoomTag.style.display = "none";
        }
        const root = document.getElementById("root");

        if (root) {
            root.style.overflow = "auto";
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function converts the color object into color string which can be used in dynamic css.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {ColorRGBA} colorObj Color object which contains red , green , blue and transparency.
     * @returns {String}
     * @method
     */
    rgbaObjectToStr = (colorObj) => {
        return (colorObj) ? 'rgba(' + colorObj.r + ', ' + colorObj.g + ', ' + colorObj.b + ', ' + colorObj.a + ')' : ''
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function prepares graphics data in terms of colorObj which can be used in dynamic css
     * as per the customisation. Directly triggers a function of dynamic css which is responsible for applying
     * all the customisation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {GraphicsData} graphics_data This variable holds the current graphics data set in redux
     * @param {Boolean} isScenery To indicate if scenery data is available or not
     * @method
     */
    implementGraphics = (graphics_data, isScenery = false) => {
        const {
            event_color_1,
            event_color_2,
            event_color_3,
            background_color,
            separation_line_color,
            text_color,
            unselected_spaces_square,
            selected_spaces_square,
            bottom_bg_color,
            has_custom_background,
            bottom_bg_is_colored,
            tag_color,
            customized_texture,
            texture_square_corner,
            texture_remove_frame,
            texture_remove_shadow,
            customized_colors,
            badge_bg_color,
            join_bg_color,
            join_text_color,
            video_url,
            professional_tag_color,
            personal_tag_color,
            sh_hide_on_off,
            sh_background,
            sh_customized,
            conv_customization,
            conv_background,
            badge_customization,
            badge_background,
            user_grid_customization,
            user_grid_background,
            user_grid_pagination_color,
            tags_customization,
            tags_text_color,
            button_customized,
            content_customized,
            content_background,
            space_customization,
            space_background,
            extends_color_user_guide
        } = graphics_data;
        const color1 = event_color_1;
        const color2 = event_color_2;
        const color3 = event_color_3


        const colorObj = {
            color1: Helper.rgbaObjectToStr(color1),
            color2: Helper.rgbaObjectToStr(color2),
            color3: Helper.rgbaObjectToStr(color3),
            hasHeaderBackground: has_custom_background,
            headerBackground: Helper.rgbaObjectToStr(background_color),
            headerTextColor: Helper.rgbaObjectToStr(text_color),
            separationLineColor: Helper.rgbaObjectToStr(separation_line_color),
            bottomBackgroundColor: Helper.rgbaObjectToStr(bottom_bg_color),
            hasBottomBackgroundColor: bottom_bg_is_colored,
            customizeTexture: customized_texture,
            textureRound: texture_square_corner,
            tagColor: Helper.rgbaObjectToStr(tag_color),
            textureWithFrame: texture_remove_frame,
            selectedSpacesSquare: selected_spaces_square,
            unselectedSpacesSquare: unselected_spaces_square,
            textureWithShadow: texture_remove_shadow,
            customizeColor: customized_colors,
            transparent: (color1) ? 'rgb(' + color1.r + ' ' + color1.g + ' ' + color1.b + ' / ' + '40%)' : '',
            badgeBgColor: Helper.rgbaObjectToStr(badge_bg_color),
            joinButtonBgColor: Helper.rgbaObjectToStr(join_bg_color),
            joinButtonTextBgColor: Helper.rgbaObjectToStr(join_text_color),
            professional_tag_color: Helper.rgbaObjectToStr(professional_tag_color),
            personal_tag_color: Helper.rgbaObjectToStr(personal_tag_color),
            sh_hide_on_off: sh_hide_on_off,
            sh_background: Helper.rgbaObjectToStr(sh_background),
            sh_customized: sh_customized,
            conv_customization: conv_customization,
            conv_background: Helper.rgbaObjectToStr(conv_background),
            badge_customization: badge_customization,
            badge_background: Helper.rgbaObjectToStr(badge_background),
            user_grid_customization: user_grid_customization,
            user_grid_background: Helper.rgbaObjectToStr(user_grid_background),
            tags_customization: tags_customization,
            tags_text_color: Helper.rgbaObjectToStr(tags_text_color),
            button_customized: button_customized,
            user_grid_pagination_color: Helper.rgbaObjectToStr(user_grid_pagination_color),
            content_customized: content_customized,
            content_background: Helper.rgbaObjectToStr(content_background),
            space_customization: space_customization,
            space_background: Helper.rgbaObjectToStr(space_background),
            extends_color_user_guide: extends_color_user_guide
        }

        this.setState({videoLink: video_url})
        CSSGenerator.generateNewInterfaceCSS(colorObj, isScenery);
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function gets data from backend and uses data to set interface graphics and localstorage.
     * it is called with token, when user is authed. Logged in.
     * -----------------------------------------------------------------------------------------------------------------
     */
    getOrganisationWithToken = () => {
        try {
            return KeepContactagent.Auth.getOrganisationWithToken()
                .then((res) => {

                    this.props.setTestAudioUrl(res.data.test_audio);
                    const {organisation_name, lang, main_color, kct_enabled} = res.data;
                    if (_.has(res.data, ['graphics_data'])) {
                        this.props.setInterfaceGraphics(res.data.graphics_data);
                        this.implementGraphics(res.data.graphics_data)
                    }
                    if (_.has(res.data, ['active_event']) && res.data.active_event) {
                        reactLocalStorage.set("active_event_uuid", res.data.active_event.uuid);
                    }
                    this.props.updateInit(res.data);

                    const {fname, lname} = res.data.auth
                    const {color1, color2, head_bg, head_tc} = main_color;
                    const colorObj = {
                        mainColor1: this.rgbaObjectToStr(color1),
                        mainColor2: this.rgbaObjectToStr(color2),
                        mainColor3: this.rgbaObjectToStr(color2),
                        headerColor: this.rgbaObjectToStr(head_bg),
                        headerTextColor: this.rgbaObjectToStr(head_tc),
                    }

                    reactLocalStorage.set("colorObj", JSON.stringify(colorObj));
                    CSSGenerator.generateDefaultCSS(colorObj)
                    reactLocalStorage.set("fname", fname)
                    reactLocalStorage.set("lname", lname)
                    if (kct_enabled) {
                        reactLocalStorage.set("current_lang", lang.current.toUpperCase());
                    }
                    reactLocalStorage.set("avail_lang", lang.enabled_languages);

                    reactLocalStorage.set('organisation_name', organisation_name);
                    reactLocalStorage.set('kct_enabled', kct_enabled ? kct_enabled : 0);

                    this.setState({kctEnabled: kct_enabled});
                    this.setState({dataLoad: true});
                    this.setGlobalRef();
                })
                .catch((err) => {
                    this.setState({dataLoad: true});

                })

        } catch (err) {
            console.error(err)
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Function Sets Global alert ref.
     * -----------------------------------------------------------------------------------------------------------------
     */
    setGlobalRef = () => {
        setTimeout(() => {
            this.msg && Helper.setGlobalRef(this.msg);
        }, 1000)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function gets data from backend and uses data to set interface graphics and localstorage.
     * it is called with token, when user is not authed. without Logged in.
     * -----------------------------------------------------------------------------------------------------------------
     */
    getOrganisation = () => {
        try {
            return KeepContactagent.Auth.getOrganisation()
                .then((res) => {
                    this.props.setTestAudioUrl(res.data.test_audio);
                    const {organisation_name, lang, main_color, kct_enabled} = res.data;
                    if (_.has(res.data, ['graphics_data'])) {
                        this.props.setInterfaceGraphics(res.data.graphics_data);
                        this.implementGraphics(res.data.graphics_data)
                    }

                    if (_.has(res.data, ['active_event']) && res.data.active_event) {
                        reactLocalStorage.set("active_event_uuid", res.data.active_event.uuid);
                    }

                    const {
                        event_color_1,
                        event_color_2,
                        event_color_3,
                        background_color,
                        separation_line_color,
                        text_color,
                        unselected_spaces_square,
                        selected_spaces_square,
                        bottom_bg_color,
                        customized_texture,
                        has_custom_background,
                        texture_round,
                        texture_with_frame,
                        texture_with_shadow
                    } = main_color;

                    const colorObj = {
                        mainColor1: this.rgbaObjectToStr(event_color_1),
                        mainColor2: this.rgbaObjectToStr(event_color_2),
                        mainColor3: this.rgbaObjectToStr(event_color_3),
                        hasHeaderBackground: has_custom_background,
                        headerBackground: this.rgbaObjectToStr(background_color),
                        headerTextColor: this.rgbaObjectToStr(text_color),
                        separationLineColor: this.rgbaObjectToStr(separation_line_color),
                        bottomBackgroundColor: this.rgbaObjectToStr(bottom_bg_color),
                        customizeTexture: customized_texture,
                        textureRound: texture_round,
                        textureWithFrame: texture_with_frame,
                        selectedSpacesSquare: selected_spaces_square,
                        unselectedSpacesSquare: unselected_spaces_square,
                        textureWithShadow: texture_with_shadow
                    }
                    reactLocalStorage.set("colorObj", JSON.stringify(colorObj));

                    CSSGenerator.generateDefaultCSS(colorObj)

                    if (kct_enabled && reactLocalStorage.get('accessToken')) {
                        reactLocalStorage.set("current_lang", lang.current.toUpperCase());
                    }

                    reactLocalStorage.set("avail_lang", lang.enabled_languages);

                    reactLocalStorage.set('organisation_name', organisation_name);
                    reactLocalStorage.set('kct_enabled', kct_enabled ? kct_enabled : 0);

                    this.setState({kctEnabled: kct_enabled});
                    this.setState({dataLoad: true});
                    this.setGlobalRef();
                    this.props.updateInit(res.data);

                })
                .catch((err) => {
                    this.setState({dataLoad: true});
                })

        } catch (err) {
            console.error(err);
        }
    }

    render() {
        const {kctEnabled, dataLoad} = this.state;

        if (!kctEnabled) {
            return (
                <>
                    <div className="full-w-h-sec">
                        <div class="full-pg-notification flexbox">
                            <h1>{KCTLocales.DISABLED_KCT}</h1>
                        </div>
                    </div>
                </>
            )
        }

        if (!dataLoad) {
            return (<Helper.pageLoading />)
        }


        return (
            <React.Fragment>
                <AlertContainer ref={(a) => {
                    this.msg = a;
                    this.msg && Helper.setGlobalRef(this.msg);
                }} {...Helper.alertOptions} />

                <Routes>
                    <Fragment>
                        <Route path="/dashboard/:event_uuid"
                               element={<PrivateRoute>{(render) => <DashboardContainer {...render} implementGraphics={this.implementGraphics} />}</PrivateRoute>} />
                        <Route path="/change-password" name="Change password"
                               element={<SimpleAuthRoute><ChangePassword /></SimpleAuthRoute>} />
                        <Route exact path="/quick-register/:event_uuid" name="Meeting login"
                               element={<SimpleAuthRoute><NewRegistration /></SimpleAuthRoute>} />
                        <Route exact path="/quick-login/:event_uuid" name="Meeting login"
                               element={<SimpleAuthRoute><NewLogin /></SimpleAuthRoute>} />
                        <Route exact path="/quick-login" name="Meeting New login"
                               element={<SimpleAuthRoute><SimpleLogin /></SimpleAuthRoute>} />
                        <Route exact path="/quick-otp/:key" name="Meeting login"
                               element={<SimpleAuthRoute><OtpProcess2 /></SimpleAuthRoute>} />
                        <Route exact path="/quick-otp" name="Meeting login"
                               element={<SimpleAuthRoute><OtpProcess2 /></SimpleAuthRoute>} />
                        <Route path="/quick-user-info/:event_uuid" name="Meeting login"
                               render={(props) => {
                                   return (<AuthLayout videoLink={this.state.videoLink} {...props}
                                                       Child={UserInfoUpdate}
                                                       implementGraphics={this.implementGraphics} />)
                               }}
                        />
                        <Route exact path="/kct-set-password/:email/:key" name="Forget Password"
                               element={<SimpleAuthRoute><ResetPassword /></SimpleAuthRoute>} />

                        <Route exact path="/forget-password" name="Forget Password"
                               element={<SimpleAuthRoute><ForgetPassword /></SimpleAuthRoute>} />
                        <Route getOrganisationWithToken={this.getOrganisationWithToken} exact
                               path="/event-list" name="Registration"
                               element={<SimpleAuthRoute><MyEventList /></SimpleAuthRoute>} />
                        <Route exact path="/event-agenda/:event_uuid" name="ViewAgenda"
                               element={<SimpleAuthRoute><MyEventList /></SimpleAuthRoute>} />
                        <Route exact path="/ml" name="Magic Link"
                               element={<SimpleAuthRoute><MagicLogin /></SimpleAuthRoute>} />

                        <Route exact path="/page-expired/:event_uuid" name="Page Expired" element={<PageExpired/>} />

                        <Route exact path="/event-list" name="Registration"
                               element={<SimpleAuthRoute><MyEventList /></SimpleAuthRoute>} />
                        {/*<SimpleAuthRoute getOrganisationWithToken={this.getOrganisationWithToken} exact*/}
                        {/*                 path="/event-list" name="Registration" component={MyEventList} />*/}
                        {/*<SimpleAuthRoute exact path="/event-agenda/:event_uuid" name="ViewAgenda"*/}
                        {/*                 component={ViewAgenda} />*/}
                        {/*<SimpleAuthRoute exact path="/ml" name="Magic Link" component={MagicLogin} />*/}

                        <Route exact path="/" element={
                            <>
                                {reactLocalStorage.get('accessToken') ?
                                    <Navigate from="/" to="/event-list" />
                                    :
                                    <Navigate from="/" to="/quick-login" />
                                }
                            </>
                        }/>
                    </Fragment>

                </Routes>
            </React.Fragment>
        )
    }
}

const mapStateToProps = (state) => {

    return {}
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateInit: (data) => dispatch({type: KCT.EVENT.UPDATE_INIT_DATA, payload: data,}),
        setInterfaceGraphics: (data) => dispatch(newInterfaceActions.NewInterFace.setInterfaceGraphics(data)),
        setEventDesignSetting: (data) => dispatch({type: KCT.EVENT.SET_EVENT_DESIGN_SETTINGS, payload: data}),
        setTestAudioUrl: (url) => dispatch(newInterfaceActions.NewInterFace.setTestAudioUrl(url)),
    }
}
AppRoutes = connect(
    mapStateToProps,
    mapDispatchToProps
)(AppRoutes);

export default withContext(AppRoutes);