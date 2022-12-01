import React, {useCallback, useEffect, useRef, useState} from "react";
import ReactCrop from "react-image-crop";
import "react-image-crop/dist/ReactCrop.css";
import "./ImgCropper.css";
import _ from "lodash";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for crop image.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @class
 * @component
 * @param {Object} props Inherited from parent component
 * @param {Number} props.aspect aspect ratio of image
 * @param {Object} props.capturedImgURL Image URL
 * @returns {JSX.Element}
 * @constructor
 */
const ImgCropper = (props) => {
    const {saveImage} = props;
    const [upImg, setUpImg] = useState();
    const imgRef = useRef(null);
    const previewCanvasRef = useRef(null);
    const [crop, setCrop] = useState({
        unit: '%', // Can be 'px' or '%'
        x: 25,
        y: 0,
        // width: 50,
        // height: 100,
        aspect: props.aspect ?? 1
    });
    const [completedCrop, setCompletedCrop] = useState(null);
    const [showCropper, setShowCropper] = useState(true);
    //data to save cropped image
    const [croppedBlob, setCroppedBlob] = useState('');

    useEffect(() => {
        cropImg();
    }, [completedCrop])

    useEffect(() => {
        if (_.has(props, ['capturedImgURL'])) {
            setShowCropper(false);
            setUpImg(props.capturedImgURL);
        }
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for selecting the file
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript Event Object
     */
    const onSelectFile = (e) => {
        if (e.target.files && e.target.files.length > 0) {
            setShowCropper(false);
            const reader = new FileReader();
            reader.addEventListener("load", () => setUpImg(reader.result));
            reader.readAsDataURL(e.target.files[0]);
        }
    };

    const onLoad = useCallback((img) => {
        imgRef.current = img;
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for crop the image.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const cropImg = () => {
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
        ctx.imageSmoothingQuality = "high";

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
        }, 'image/png');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for save image
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const saveImg = () => {
        const files = new Blob([croppedBlob], {type: croppedBlob.type}, croppedBlob.name)
        saveImage && saveImage(files);
        // //top remove previousely drawn obj
        // const canvas = previewCanvasRef.current;
        // const ctx = canvas.getContext("2d");
        // ctx.clearRect(0, 0, canvas.width, canvas.height);

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for cancel the image cropping
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const cancelCropImg = () => {
        setShowCropper(true);
        setUpImg("")
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling the  crop image
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} c Crop value of image
     */
    const completeHandler = (c) => {
        setCompletedCrop(c);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is handle the loading image uploading
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript Event Object
     */
    const onImageLoadHandler = (e) => {
        let img = e.currentTarget

        const aspect = 1
        const imgWidth = img.width / aspect < img.height * aspect ? 100 : ((img.height * aspect) / img.width) * 100
        const imgHeight = img.width / aspect > img.height * aspect ? 100 : (img.width / aspect / img.height) * 100
        const imgY = (100 - imgHeight) / 2
        const imgX = (100 - imgWidth) / 2

        if (img.width < img.height) {
            setCrop({
                unit: '%', // Can be 'px' or '%'
                x: imgX,
                y: imgY,
                width: 100,
                aspect: props.aspect ?? 1
            })
        } else {
            setCrop({
                unit: '%', // Can be 'px' or '%'
                x: imgX,
                y: imgY,
                height: 100,
                aspect: props.aspect ?? 1
            })
        }

        setCompletedCrop({
            x: imgX,
            y: imgY,
        })

        // setCrop(crop)
    }

    return (
        <div className="Img_cropper_main">
            {showCropper ? (
                <>
                    {_.has(props, ['capturedImgURL']) ?
                        ""
                        :
                        <div className="selectImg">
                            <input type="file" accept="image/*" onChange={onSelectFile} />
                        </div>
                    }
                </>
            ) : (
                <div>
                    <ReactCrop
                        src={_.has(props, ['capturedImgURL']) ? props.capturedImgURL : upImg}
                        onImageLoaded={onLoad}
                        crop={crop}
                        minHeight={50}
                        minWidth={50}
                        onChange={(c) => setCrop(c)}
                        onComplete={completeHandler}
                        className="Img_cropper_tool"
                        id="preview_img_cropper"
                    >
                        <img src={_.has(props, ['capturedImgURL']) ? props.capturedImgURL : upImg}
                             onLoad={onImageLoadHandler} style={{display: "none"}} />
                    </ReactCrop>

                    <canvas
                        ref={previewCanvasRef}
                        id="Img_cropper_canvas"
                        // Rounding is important so the canvas width and height matches/is a multiple for sharpness.
                        style={{
                            width: 300,
                            height: 300,
                            display: "none",
                            // width: Math.round(completedCrop?.width ?? 0),
                            // height: Math.round(completedCrop?.height ?? 0)
                        }}
                    />
                </div>
            )}


            {/* <button onClick={cropImg}>Crop</button> */}
            {!_.has(props, ['capturedImgURL']) &&
            <button onClick={cancelCropImg} className="btn_outline_dark">Cancel</button>}

            <button onClick={saveImg}
                    className={`btn_outline_dark ${_.has(props, ['capturedImgURL']) ? "ImgBtn_absolute" : ""}`}>Save
            </button>
        </div>
    );
}

export default ImgCropper;

