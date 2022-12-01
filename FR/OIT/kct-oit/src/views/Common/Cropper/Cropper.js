import React, {useState, useCallback, useRef, useEffect} from "react";
import ReactCrop from "react-image-crop";
import "react-image-crop/dist/ReactCrop.css";
import Button from '@mui/material/Button';
import "./Cropper.css";
import _ from "lodash";
import CircularProgress from '@mui/material/CircularProgress';
import ResponsiveDialog from "../DialogBox/DialogBox";
import CroppedImageObject from "../../../Models/CroppedImageObject";


/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to crop any image(clicked/uploaded).
 * ** Current package(npm) version - 9.1.1 **
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received function to save cropped image, show loader as props from the component
 * where it is called eg - currently is is used in create event>Live tab.
 * @param {Function} props.callBack Function to save cropped image blob's value(in file object form)
 * @param {String} props.isImageUploader String to manage rendering of cropper's own image input component.
 * @param {Boolean} props.showLoader Boolean to show loader
 * @param {String} props.capturedImgURL Image URL in the form of BASE64
 * @param {Function} props.saveImage Function to save cropped image
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
function Cropper(props) {
    const [upImg, setUpImg] = useState();
    const imgRef = useRef(null);
    const inputRef = useRef(null);
    const previewCanvasRef = useRef(null);
    const [crop, setCrop] = useState({
        aspect: props.aspect
    });
    const [completedCrop, setCompletedCrop] = useState(null);
    //state to manage modal open/close
    const [dialogBoxOpen, setDialogBoxOpen] = useState(false)
    //data to save cropped image
    const [croppedBlob, setCroppedBlob] = useState('');
    //this state are only to manage preview for design setting - currently implementing in content setting 
    const [showCroppedImg, setShowCroppedImg] = useState(false)
    const [CroppedImgUrl, setCroppedImgUrl] = useState("")
    const [showLoader, setShowLoader] = useState(false)

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger a callback function to save cropped image for event image.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} fileObj Object of the file
     */
    const fileChange = (fileObj) => {
        if (fileObj) {
            props.callBack(
                {
                    field: props?.keyName,
                    value: fileObj
                }
            )
        }
    }
    useEffect(() => {
        if (_.has(props, ["isImageUploader"]) && croppedBlob) {
            const files = new Blob([croppedBlob], {type: croppedBlob?.type}, croppedBlob?.name)
            console.log('completedCrop---', files)
            fileChange(files)
        }
    }, [croppedBlob])

    useEffect(() => {
        if (!props.showLoader) {
            setDialogBoxOpen(false);
        }
    }, [props.showLoader])

    useEffect(() => {
        if (!showLoader) {
            setDialogBoxOpen(false);
        }
    }, [showLoader])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user select an image from syatem to crop.Once the image get
     * selected then it will open in in a popup component to perform crop action.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e JavaScript event object.
     */
    const onSelectFile = (e) => {
        if (e.target.files && e.target.files.length > 0) {
            var binaryData = [];
            binaryData.push(e.target.files[0]);
            const base_url = window.URL.createObjectURL(
                e.target.files[0]
                // new Blob(binaryData, { type: "image/png" })
            );
            setUpImg(base_url)
            setDialogBoxOpen(true);
        }
        e.target.value = null;
    };

    const onLoad = useCallback((img) => {
        imgRef.current = img;
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take coordinates of selected area from crop component and draw it on canvas.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {CroppedImageObject} completedCrop  Cropped image value object
     */
    const cropImg = (completedCrop) => {
        if (!completedCrop || !previewCanvasRef.current || !imgRef.current) {
            return;
        }
        const image = imgRef.current;

        const canvas = previewCanvasRef.current;
        const crop = completedCrop;

        const scaleX = image.naturalWidth / image.width;
        const scaleY = image.naturalHeight / image.height;
        const ctx = canvas.getContext("2d");
        const pixelRatio = window.devicePixelRatio;

        canvas.width = crop.width * pixelRatio * scaleX;
        canvas.height = crop.height * pixelRatio * scaleY;

        ctx.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        // ctx.imageSmoothingQuality = "low";

        //top remove previousely drawn obj
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.drawImage(
            image,
            crop.x * scaleX,
            crop.y * scaleY,
            crop.width * scaleX,
            crop.height * scaleY,
            0,
            0,
            crop.width * scaleX,
            crop.height * scaleY
        );
        canvas.toBlob((blob) => {
            setCroppedBlob(blob);
        }, 'image/jpeg', 0.5);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will create a blob for cropped image and pass it to 'saveImage' function to save.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const saveImg = () => {
        const files = new Blob([croppedBlob], {type: croppedBlob?.type}, croppedBlob?.name)
        if (_.has(props, ["isImageUploader"])) {
            props.updateDesignSetting(null, setShowLoader);
            // setDialogBoxOpen(false);
        } else {
            props.saveImage(files);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will cancel the crop action and close the crop popup component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const cancelCropImg = () => {
        if (_.has(props, ["isImageUploader"])) {
            setShowCroppedImg(false)
        }
        setDialogBoxOpen(false);
        setTimeout(() => {
            setUpImg("")
        }, 500)
        // inputRef.current.value("")
        if (inputRef.current.value !== null) {
            inputRef.current.value = null
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user release mouse after select cropped area.This function will
     * take coordinates of selected area and pass them is a function(setCompletedCrop,cropImg) to perform crop.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {CroppedImageObject} c Cropped image value object which consist width,height,aspect ratio and x,y
     * coordinates.
     */
    const completeHandler = (c) => {
        setCompletedCrop(c);
        cropImg(c)
        console.log(c);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user select an image from system and this function will calculate
     * the selected image size(landscape & portrait) to adjust image size as per popup component(crop).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e JavaScript event object
     */
    const onImageLoadHandler = (e) => {
        let img = e.currentTarget
        // check 
        const aspect = props?.aspect
        const imgWidth = img.width / aspect < img.height * aspect ? 100 : ((img.height * aspect) / img.width) * 100
        const imgHeight = img.width / aspect > img.height * aspect ? 100 : (img.width / aspect / img.height) * 100
        const imgY = (100 - imgHeight) / 2
        const imgX = (100 - imgWidth) / 2

        setCrop({
            unit: '%', // Can be 'px' or '%'
            x: imgX,
            y: imgY,
            width: imgWidth,
            height: imgHeight,
            aspect: props.aspect
        })
    }

    return (
        <div className="Img_cropper_main">
            <div className="selectImg">
                <input ref={inputRef} type="file" accept=".png, .jpg, .jpeg" disabled={props?.disabled}
                       onChange={onSelectFile}
                       onClick={(e) => e.target.value = null}
                />
            </div>
            <div>
                <ResponsiveDialog
                    setDialogBoxOpen={setDialogBoxOpen}
                    dialogBoxOpen={dialogBoxOpen}
                    subHeahing={props?.subHeading}
                    child={
                        <>
                            {props.showLoader && <CircularProgress className="crop_loader" />}
                            {showLoader && <CircularProgress className="crop_loader" />}
                            <ReactCrop
                                src={upImg}
                                onImageLoaded={onLoad}
                                crop={crop}
                                minHeight={50}
                                minWidth={50}
                                onChange={(c) => setCrop(c)}
                                onComplete={completeHandler}
                                className="Img_cropper_tool"
                            >
                                <img
                                    src={
                                        _.has(props, ['capturedImgURL'])
                                            ? props.capturedImgURL
                                            : upImg
                                    }
                                    onLoad={onImageLoadHandler}
                                    style={{display: "none"}}
                                />
                            </ReactCrop>
                        </>
                    }
                    actionButtons={
                        <>
                            <Button variant="outlined" onClick={cancelCropImg} className="btn_outline_dark">
                                Cancel
                            </Button>
                            <Button variant="contained" onClick={saveImg} className={`btn_outline_dark`}>
                                Save
                            </Button>
                        </>
                    }
                />

                <canvas
                    ref={previewCanvasRef}
                    id="Img_cropper_canvas"
                    // className={`show_cropped_img`}
                    // Rounding is important so the canvas width and height matches/is a multiple for sharpness.
                    style={{
                        display: `${showCroppedImg ? "block" : "none"}`,
                        display: `none`,
                    }}
                />
            </div>
        </div>
    );
}

export default Cropper;