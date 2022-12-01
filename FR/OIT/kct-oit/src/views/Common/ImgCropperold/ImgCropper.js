import React, {useState, useCallback, useRef, useEffect} from "react";
import ReactCrop from "react-image-crop";
import "react-image-crop/dist/ReactCrop.css";
import "./ImgCropper.css";
import _ from "lodash";

/**
 * @deprecated
 */
export default function ImgCropper(props) {
    const {saveImage} = props;
    const [upImg, setUpImg] = useState();
    const imgRef = useRef(null);
    const previewCanvasRef = useRef(null);
    const [crop, setCrop] = useState({
        unit: "%",
        width: 16,
        height: 9,
        aspect: 16 / 9,
    });
    const [completedCrop, setCompletedCrop] = useState(null);
    const [showCropper, setShowCropper] = useState(true);
    //data to save cropped image
    const [croppedBlob, setCroppedBlob] = useState("");
    const [blobfile, setBlobFile] = useState('')

    useEffect(() => {
        cropImg();

    }, [completedCrop]);

    useEffect(() => {
        if (_.has(props, ["capturedImgURL"])) {
            setShowCropper(false);
            setUpImg(props.capturedImgURL);
        }
    }, []);

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
        cropImg()
    }, []);

    const cropImg = () => {
        if (!completedCrop || !previewCanvasRef.current || !imgRef.current) {
            return;
        }
        const image = imgRef.current;
        const canvas = previewCanvasRef.current;
        // const canvas =imgRef.current
        const crop = completedCrop;
        const scaleX = image.target.naturalWidth / image.target.width;
        const scaleY = image.target.naturalHeight / image.target.height;
        const ctx = canvas.getContext("2d");
        const pixelRatio = window.devicePixelRatio;
        canvas.width = crop.width //* pixelRatio * scaleX;
        canvas.height = crop.height //* pixelRatio * scaleY;

        // ctx.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        ctx.imageSmoothingQuality = "high";

        //top remove previousely drawn obj
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.drawImage(
            image.target,
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
            var binaryData = [];
            binaryData.push(blob);
            const base_url = window.URL.createObjectURL(
                new Blob(binaryData, {type: "image/png"})
            );
            setBlobFile(base_url)

        }, "image/png");
    };

    const saveImg = () => {
        const files = new Blob(
            [croppedBlob],
            {type: croppedBlob.type},
            croppedBlob.name
        );
        saveImage && saveImage(files);

    };
    const cancelCropImg = () => {
        setShowCropper(true);
        setUpImg("");
    };

    const completeHandler = (c) => {
        setCompletedCrop(c);
    };

    return (
        <div className="Img_cropper_main">
            {showCropper ? (
                <>
                    {_.has(props, ["capturedImgURL"]) ? (
                        ""
                    ) : (
                        <div className="selectImg">
                            <input type="file" accept="image/*" onChange={onSelectFile} />
                        </div>
                    )}
                </>
            ) : (
                <div>
                    {_.has(props, ["capturedImgURL"]) &&
                    <ReactCrop
                        onComplete={completeHandler}
                        crop={crop}
                        minHeight={28}
                        minWidth={50}
                        onChange={(c) => setCrop(c)}
                        aspect={16 / 9}
                        className="Img_cropper_tool"
                        crossorigin='anonymous'
                    >
                        <img alt="Crop me" src={props.capturedImgURL} onLoad={onLoad} />
                    </ReactCrop>

                    }
                    <canvas
                        ref={previewCanvasRef}
                        id="Img_cropper_canvas"
                        // Rounding is important so the canvas width and height matches/is a multiple for sharpness.
                        style={{
                            width: 300,
                            height: 300,
                            display: "none",
                        }}
                    />
                    <image />

                </div>

            )}


            <button
                onClick={saveImg}
                className={`btn_outline_dark ${_.has(props, ["capturedImgURL"]) ? "ImgBtn_absolute" : ""
                }`}
            >
                Save
            </button>

        </div>
    );
}
