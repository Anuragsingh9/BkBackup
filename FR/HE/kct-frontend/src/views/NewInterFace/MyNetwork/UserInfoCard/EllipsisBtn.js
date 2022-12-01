import React from "react";

const EllipsisOptionBtn = (props) => {
    return (
        <div className="dropdown">
            <button className="btn btn-secondary " type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i className="fa fa-ellipsis-v" aria-hidden="true"></i>
            </button>
            <div className="dropdown-menu pull-right" aria-labelledby="dropdownMenuButton">
                {props?.options.map((option, index) => (
                    <a className="dropdown-item" href="#">{option}</a>
                ))}
            </div>
        </div>
    )
}

export default EllipsisOptionBtn;