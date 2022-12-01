import React from 'react'
import Helper from '../Helper'

const _ = require('lodash')

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the different types of the input box based on the passed type
 * which are modified to follow the application design
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {JSX.Element}
 * @class
 * @constructor
 * @deprecated
 */
const FormInputs = function ({
                                 children,
                                 disabled,
                                 input,
                                 label,
                                 type,
                                 infoLabel,
                                 labelClass,
                                 isFilePreview,
                                 filePreviewPath,
                                 onFileRemove,
                                 accept,
                                 showTransparency,
                                 isCrossShow,
                                 calenderType,
                                 isFileExist,
                                 textType,
                                 defaultValue,
                                 adobeStock,
                                 toggleAdobe,
                                 showPreviewImage,
                                 meta: {touched, error, warning}
                             }) {
    // here are the types of the different FormInput
    switch (type) {
        case 'textarea':
            input.value = _.capitalize(input.value)
            return (
                <div className="form-group">
                    <label htmlFor={input.name} className={labelClass}>
                        {label} &nbsp;&nbsp; <span className="disable-line">{(infoLabel) ? `(${infoLabel})` : ''}</span>
                    </label>
                    <textarea className="form-control" {...input} id={input.name} placeholder={label} />
                    {touched &&
                    ((error && <span className="text-danger">{error}</span>) ||
                        (warning && <span>{warning}</span>))}
                </div>
            )
        case 'select':
            return (
                <div className="form-group">
                    <label htmlFor={input.name} className={labelClass}>
                        {label} &nbsp;&nbsp; <span className="disable-line">{(infoLabel) ? `(${infoLabel})` : ''}</span>
                    </label>
                    <select disabled={disabled} className="form-control" {...input} id={input.name} placeholder={label}>
                        {children}
                    </select>
                    {touched &&
                    ((error && <span className="text-danger">{error}</span>) ||
                        (warning && <span>{warning}</span>))}
                </div>
            )
        case 'file':
            const onFileChange = (e) => {
                e.preventDefault();
                const files = [...e.target.files];
                input.onChange(files[0]);
                input.onBlur()
            };
            let showCrossButton = (isCrossShow != undefined) ? isCrossShow : true
            let fileName = ""
            if (isFilePreview && filePreviewPath) {
                fileName = (_.isObject(input.value)) ? input.value.name : input.value
            }
            return (
                <div className="form-group">
                    <label className={labelClass}>
                        {label}<span className="disable-line">{(infoLabel) ? `(${infoLabel})` : ''}</span>
                    </label>

                    {adobeStock ?
                        <div className="d-inline clearfix w-100">
                            <div className="choose-file-dropdown btn-group dropdown w-100">
                                <button type="button" className="btn choose-file-label-btn">
                                    <i className="fa fa-file-o" aria-hidden="true"></i> Choose File
                                </button>
                                <button type="button" className="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i className="fa fa-angle-down"></i>
                                </button>
                                <div className="dropdown-menu">
                                    <ul className="pl-0 mb-0 cursor-pointer">
                                        <li className="upload-from-device">
                                            {/* <span className="svgicon svg-20" dangerouslySetInnerHTML={{__html:SVG.COCKTAIL_ICON.folder}} ></span> */}
                                            <input onChange={onFileChange} component="input"
                                                   accept={accept ? accept : ''} type="file" className="file" />
                                            <div className="input-group">
                                                <input name={`${input.name}Field`} value={fileName} disabled={true}
                                                       type="text" autocapitalize="none" className="form-control" />
                                                <span className="browse">Form Computer</span>
                                            </div>
                                        </li>
                                        <li>
                                            {/* <span className="svgicon svg-20" dangerouslySetInnerHTML={{__html:SVG.COCKTAIL_ICON.server}} ></span> */}
                                            <div onClick={toggleAdobe}>Adobe stock</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        :
                        <span>
                        <input onChange={onFileChange} component="input" accept={accept ? accept : ''} type="file"
                               className="file" />
                        <div className="input-group">
                            <input name={`${input.name}Field`} value={fileName} disabled={true} type="text"
                                   autocapitalize="none" className="form-control" />
                            <span className="input-group-btn">
                                <button className="browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                    </span>
                    }
                    {touched &&
                    ((error && <span className="text-danger">{error}</span>) ||
                        (warning && <span>{warning}</span>))}
                    {!error && isFilePreview && filePreviewPath && showPreviewImage &&
                    <div className="uploadProfile-img mt-10"
                         style={{background: `url(${filePreviewPath})`}}>
                        {(showCrossButton && isFileExist) &&
                        <span className="crossBtn"
                              onClick={(e) => onFileRemove && onFileRemove(input.name)}>X</span>
                        }
                    </div>
                    }
                </div>
            )
        case "switch":
            return (
                <div className="form-group">
                    <label className="switch">
                        <input {...input} id={input.name} disabled={(disabled) ? true : false} type="checkbox"
                               checked={(input.value)} value="1" /><strong className="slider"></strong>
                    </label>
                    <span className={labelClass}>{label}</span>
                    {touched &&
                    ((error && <span>{error}</span>) ||
                        (warning && <span>{warning}</span>))}
                </div>
            )
        default:
            if (textType !== 'url' && textType !== 'email') {
                input.value = Helper.jsUcfirst(input.value)
                defaultValue = Helper.jsUcfirst(defaultValue)
            }
            return (
                <div className="form-group">
                    <label htmlFor={input.name} className={labelClass}>
                        {label} &nbsp;&nbsp; <span className="disable-line">{infoLabel}</span>
                    </label>
                    <input className="form-control" {...input} name={input.name}
                           value={(input.value) ? input.value : (defaultValue || '')} id={input.name}
                           placeholder={label} type={type} />
                    {touched &&
                    ((error && <span className="text-danger">{error}</span>) ||
                        (warning && <span>{warning}</span>))}
                </div>
            )
            break;
    }
}

FormInputs.defaultProps = {
    showPreviewImage: true
}

export default FormInputs;