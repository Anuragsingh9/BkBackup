import React, {useEffect} from "react";
import {useHistory, useNavigate, useParams} from "react-router-dom";
import Header from "../../NewInterFace/Header/Header";
import Footer from "../../NewInterFace/Footer/Footer";
import {connect} from "react-redux";
import {reactLocalStorage} from "reactjs-localstorage";

const PageExpired = (props) => {

    let history = useNavigate();
    const {event_uuid} = useParams();

    useEffect(() => {
        // Clearing the local storage on mounting of the component
        reactLocalStorage.set('accessToken', '');
        reactLocalStorage.set('fname', '');
        reactLocalStorage.set('lname', '');
        reactLocalStorage.set('email', '');
    },[])

    const handleClick = () => {
        history.push(`/quick-login/${event_uuid}`);
    }

    return (
        <div>
            <Header
                event_data={{
                    header_line_one: '',
                    header_line_two: ''
                }}
                dropdown={false}
            />
            <div className="content-height">
                <div className="container">
                    <>
                        <div style={{ margin: "30 0", fontSize:35, textAlign:"center"}}>
                            The link has been expired. Please login again to get the link.
                        </div>
                        <div style={{textAlign:"center"}}>
                            <button style={{border:"1px solid", padding: 7, borderRadius: 3,backgroundColor: "#249ed9",color:"white",margin:10,width:100}} onClick={handleClick}>Login</button>
                        </div>
                    </>
                </div>
            </div>
            <Footer graphics_data={props.graphics_data} />
        </div>
    );

}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
    };
};

export default connect(
    mapStateToProps,
    null
)(PageExpired);