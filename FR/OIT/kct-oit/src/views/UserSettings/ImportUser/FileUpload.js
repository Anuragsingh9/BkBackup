import React, {useState} from 'react';
import _ from 'lodash';
import TextField from '@material-ui/core/TextField';
import {Button} from '@material-ui/core';
import {useDispatch} from 'react-redux';
import {useAlert} from 'react-alert';
import Link from '@material-ui/core/Link';
import Helper from '../../../Helper';
import userAction from '../../../redux/action/apiAction/user';
import './ImportUser.css';

//sample file(import user) link
const sampleFile = 'https://s3.eu-west-2.amazonaws.com/kct-dev/assets/user-import-template.xlsx';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed as a first step of import user process.This component contain a file
 * uploader section(user can upload an file-excel to import users from it) and a button from where user can download a
 * sample file(import user) in just one click.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.callBack Function to navigate steps in import user process.
 * @param {Function} props.handleNext This method will allow user to move to next step in user Import process
 * @param {Function} props.step3callBack This method will allow user to move directly to the third step if
 * template matched
 * @return {JSX.Element}
 */
const FileUpload = (props) => {
    console.log('props122', props)
    const preventDefault = (event) => event.preventDefault();
    const dispatch = useDispatch();
    const alert = useAlert();
    const [fileName, setFileName] = useState('');
    const [file, setFile] = useState(null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on 'choose file' button from interface(1st step of import
     * user) and this function will perform a click event on input type file component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const chooseFile = () => {
        const input = document.getElementById('fileUpload');
        if (input) {
            input.click();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user select a file(for import user) from his local system to upload.
     * Once the file uploaded successfully then it will update all related states(setFile, setFileName).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const fileChange = (e) => {
        const files = e.target.files;
        if (files[0]) {
            const fileData = files[0];
            setFile(fileData);
            setFileName(fileData.name);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to submit the uploaded file(for import user) on server.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const fileSubmit = () => {
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            try {
                dispatch(userAction.userImportFile(formData)).then((res) => {
                    if (res.data.data && res.data.data.multi_sheets_found) {
                        alert.show('Multi sheets found, File Uploaded.');
                    } else {
                        alert.show('File Uploaded.');
                    }
                    if (_.has(res.data, ['errors'])) {
                        return props.step3CallBack({...res.data, error: res.data.errors, match_template: 1});
                    }
                    if ((res.data.data && res.data.data.match_template)) {
                        props.step3CallBack(res.data.data);
                    } else {
                        props.callBack(res.data.data);
                    }
                }).catch(err => {
                    alert.show(Helper.handleError(err), {type: 'error'});
                })
            } catch (err) {
                alert.show(Helper.handleError(err), {type: 'error'});
            }
        } else {
            alert.show('File Is Required', {type: 'error'});
        }
    }

    return (
        <div className="mainImportUserDiv">
            <div className="importUserDiv">
                <p>Choose File:</p>
                <TextField value={fileName} disabled={true} label="" variant="outlined" />
                <input
                    type="file"
                    id="fileUpload"
                    style={{display: 'none'}}
                    onChange={fileChange}
                    accept=".csv,
                        application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                        application/vnd.ms-excel"
                />
                <Button variant="contained" className="theme-btn" color="primary" onClick={chooseFile}>
                    Choose File
                </Button>
            </div>
            <div className="uploadListBtn">
                <Button variant="contained" color="primary" onClick={fileSubmit}>
                    Upload
                </Button>
                <Link href={sampleFile}>
                    Download Sample File
                </Link>
            </div>
        </div>
    )
}

export default FileUpload;