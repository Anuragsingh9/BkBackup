import React, {useEffect} from "react";
import newInterfaceActions from '../../../redux/actions/newInterfaceAction';
import _ from "lodash";
import {connect} from "react-redux";
import Constants from "../../../Constants";
import ChimeBGService from "../../VideoMeeting/ChimeBGService";
import {Button} from "react-bootstrap";
import Helper from "../../../Helper";
import {useAlert} from 'react-alert'
import {useTranslation} from "react-i18next";

const customBgOptions = [
    {name: "None", type: Constants.CHIME_BG.TYPE.NONE},
    {name: "Background Blur", type: Constants.CHIME_BG.TYPE.BLUR},
    {name: "System Image", type: Constants.CHIME_BG.TYPE.SYSTEM},
    {name: "Custom Color", type: Constants.CHIME_BG.TYPE.STATIC},
];

const MediaGrid = (props) => {
    const alert = useAlert()
    const {t} = useTranslation('mediaDevicePopup')

    useEffect(() => {
        ChimeBGService.applyBackground(props.selectedBgOption.type, props.selectedBgOption.value);
    }, [props.selectedBgOption]);

    // Show alert when we change video background
    useEffect(() => {
        alert.show(t("Video background update"), {
            type: "info",
        });
    }, [props?.selectedBgOption.type]);

    useEffect(() => {
        let canvas = document.getElementById('blurBox');
        const ctx = canvas.getContext("2d");
        const grd = ctx.createLinearGradient(0, 0, 18, 70);


        let startColor = Helper.rgba2hex(props.eventGraphics.event_color_1);
        let endColor = Helper.rgba2hex(props.eventGraphics.event_color_2);


        grd.addColorStop(0, startColor);
        grd.addColorStop(1, endColor);
        ctx.fillStyle = grd;
        ctx.fillRect(0, 0, 128, 80);
        ctx.font = '0.875rem Lato';
        ctx.fillStyle = '#FFF';
        ctx.fillText("Background Blur", 15, 45);
    }, [])


    const hiddenSystemSelect = React.useRef(null);

    const handleBGFileSelect = (file) => {
        if (!file) {
            props.setSelectedBgOption({type: Constants.CHIME_BG.TYPE.NONE, value: null});
        } else {
            props.setSelectedBgOption({type: Constants.CHIME_BG.TYPE.SYSTEM, value: file});
        }
    }

    const handleBGTypeSelect = (index) => {
        localStorage.setItem('chime_bg_type', customBgOptions[index].type);
        props.setSelectedBgOption({
            type: customBgOptions[index].type,
            value: null,
        })
    }

    return (
        <div className="bgTileFlexDiv">
            {customBgOptions
                && customBgOptions.map(
                    (backgroundOption, index) => {
                        return backgroundOption.type === Constants.CHIME_BG.TYPE.SYSTEM ?
                            <>
                                <Button onClick={() => hiddenSystemSelect.current.click()}
                                    className={`bgTileOption ${props?.selectedBgOption.type === backgroundOption.type ? "activeBgOption" : ""} bgUploadButton`}
                                >
                                    <i className="fa fa-upload"></i> &nbsp; Upload
                                </Button>
                                <input type={"file"}
                                    ref={hiddenSystemSelect}
                                    onChange={(event) => handleBGFileSelect(event.target.files[0])}
                                    style={{display: "none"}}
                                />
                            </>
                            :
                            (
                                backgroundOption.type === Constants.CHIME_BG.TYPE.STATIC ?
                                    <>

                                    </>
                                    :
                                    <div
                                        className={`bgTileOption ${props?.selectedBgOption.type === backgroundOption.type ? "activeBgOption" : ""}`}
                                        style={{backgroundImage: `url(${backgroundOption.bgImage})`}}
                                        key={index}
                                        onClick={() => handleBGTypeSelect(index)}
                                    >
                                        {backgroundOption.type === Constants.CHIME_BG.TYPE.BLUR ? <>
                                            <canvas id={"blurBox"} className={".br-4px"} width={128} height={80} />
                                        </>
                                            :
                                            _.has(backgroundOption, ['name']) ? backgroundOption.name : ''
                                        }
                                    </div>
                            )
                    }
                )}
        </div>
    );
}
// export default MediaGrid;

const mapDispatchToProps = (dispatch) => {
    return {
        setSelectedBgOption: (selectedOption) => dispatch(newInterfaceActions.NewInterFace.setSelectedBgOption(selectedOption)),
    }
}

const mapStateToProps = (state) => {
    return {
        selectedBgOption: state.NewInterface.selectedBgOption,
        eventGraphics: state.Graphics.eventGraphics,
    };
};


export default connect(mapStateToProps, mapDispatchToProps)(MediaGrid);

