import React from 'react'
import {Select} from "@mui/material";

const SelectField = (props) => {
    return (
        <React.Fragment>
            <Select
                name={props?.name}
                size="small"
                variant={props.variant || "filled"}
                className={props?.className}
                disabled={props?.disabled}
                value={props?.value}
                defaultValue={props?.defaultValue}
                onOpen={props?.onOpen}
                onClose={props?.onClose}
                onChange={props?.onChange}
                color="primary"
                MenuProps={{
                    anchorOrigin: {
                        vertical: "bottom",
                        horizontal: "left"
                    },
                    transformOrigin: {
                        vertical: "top",
                        horizontal: "left"
                    },
                    getContentAnchorEl: null
                }}
                // error={true}
                // helperText={"This is error"}
            >
                {
                    props?.children 
                }
            </Select>
            {/* {touched && error &&<span className={'text-danger'}>{error}</span>} */}
        </React.Fragment>
    )
}

export default SelectField