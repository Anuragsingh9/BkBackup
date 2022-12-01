import React, {useEffect, useState} from 'react';
import "./Hstatus.css";

/**
 * @deprecated
 */
const Hstatus = () => {
    const [hStatus, sethStatus] = useState('h_status_gray');


    function change_hState(){
        if (hStatus == "h_status_gray"){
            sethStatus("h_status_green");
        }else if(hStatus == "h_status_green"){
            sethStatus("h_status_yellow");
        }else if(hStatus == "h_status_yellow"){
            sethStatus("h_status_red");
        }else if(hStatus == "h_status_red"){
            sethStatus("h_status_gray");
        }

    }

    return (
        <div className={`H_status_wrap ${hStatus}`} onClick={change_hState}>
            
        </div>
    )
}
export default Hstatus
