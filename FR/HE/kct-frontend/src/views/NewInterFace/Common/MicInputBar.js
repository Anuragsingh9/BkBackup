import React, {useEffect, useState} from 'react';
import ProgressBar from 'react-bootstrap/ProgressBar'
import {stopAudioStream} from "../Conversation/Utils/Conversation";
import Svg from '../../../Svg';
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import "./MicInputBar.css"
import {useTranslation} from "react-i18next";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render incoming sound intensity from the selected device for mic in
 * media popup component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.deviceId Id of audio output device to which the test audio needs to be played
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const MicInputBar = (props) => {
    const {t} = useTranslation("mediaDevicePopup");
    const [volume, setVolume] = useState(0);
    const [currentStream, setCurrentStream] = useState(null);
    const [audioContext, setAudioContext] = useState(null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take selected device id fro mic and pass it to 'initAudioContext' function to
     * calculate voice intensity for this selected device.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {MediaStream} stream Media stream prepared from navigator with current device id
     */
    const navigatorSuccessHandler = function (stream) {
        stopAudioStream(currentStream);
        setCurrentStream(stream);
        if (audioContext) {
            audioContext.close().then(() => {
                initAudioContext(stream);
            });
        } else {
            initAudioContext(stream);
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to analyze voice coming from selected device(mic) and calculate the volume
     * in terms of percentage to show voice intensity in bar component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {MediaStream} stream Media stream prepared from navigator with current device id
     */
    const initAudioContext = (stream) => {
        let audioContext = new AudioContext();
        setAudioContext(audioContext);
        const analyser = audioContext.createAnalyser();
        const microphone = audioContext.createMediaStreamSource(stream);
        const scriptProcessor = audioContext.createScriptProcessor(2048, 1, 1);

        analyser.smoothingTimeConstant = 0.8;
        analyser.fftSize = 1024;
        microphone.connect(analyser);
        analyser.connect(scriptProcessor);
        scriptProcessor.connect(audioContext.destination);
        scriptProcessor.onaudioprocess = function () {
            const array = new Uint8Array(analyser.frequencyBinCount);
            analyser.getByteFrequencyData(array);
            const arraySum = array.reduce((a, value) => a + value, 0);
            const average = arraySum / array.length;
            setVolume(Math.round(average));
        };
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is written to handle error in navigator(method to handle external devices from
     * the browser) method.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Error} err Error object thrown from fetching data
     */
    const navigatorErrorHandler = (err) => {
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to start emmit audio from selected device by using browser API(getUserMedia).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const startAudio = () => {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia && props.deviceId) {
            const navigatorConstraints = {audio: {deviceId: props.deviceId}};
            // getting access for device
            navigator.mediaDevices.getUserMedia(navigatorConstraints).then(
                c => navigatorSuccessHandler(c)
            ).catch(e => navigatorErrorHandler(e));
        }
    }

    useEffect(() => {
        startAudio();
    }, [props.deviceId])


    useEffect(() => {
        return () => {
            stopAudioStream(currentStream);
        }
    }, [currentStream]);

    return (
        <div className='volume_progress'>
            <div>
                <div>
                    <Row className="justify-content-md-center custom_layout_specing">
                        <Col xs lg="2">
                            <div className="svgicon dummy-user"
                                 dangerouslySetInnerHTML={{__html: Svg.ICON.volume_mic_icon}}
                            ></div>
                        </Col>
                        <Col lg="10" className='progressWrap'>
                            <ProgressBar variant="success" now={volume} />
                        </Col>
                        <Col xs lg="2">
                        </Col>
                        <Col xs lg="9" className='progressWrap'>
                            <p>{t("Test Microphone")}</p>
                        </Col>
                    </Row>
                </div>
            </div>
        </div>
    )
}


export default MicInputBar;

