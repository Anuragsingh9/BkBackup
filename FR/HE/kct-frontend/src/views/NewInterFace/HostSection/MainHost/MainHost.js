import React from 'react';
import "../HostSection.css";
import {connect} from "react-redux";
// import ZoomPlayer from './ZoomPlayer/ZoomPlayer.js';
// import EventImage from './EventImage/EventImage.js';

/**
 * @deprecated
 */
class MainHost extends React.Component {
    render() {
        const openPlayer = true;
        const {event_image, embedded_url, conference_type} = this.props.event_data;
        const {event_during, event_data} = this.props;
        return (
            <section className="host-section">
                <div className="container">
                    <div className="row">
                        {/* <div className="col-md-4 col-sm-4"></div> */}
                        <div className="col-md-4 col-sm-4 main-host ">
                            <div className="text-center position-relative host-outer kct-customization">
                                <img className="img-fluid" src={this.props.main_host_data.main_hosts[0].avatar}
                                     alt="" />
                                <h6>{this.props.main_host_data.main_hosts[0].fname}</h6>


                                <ul className="d-inline-block host-left-icon">
                                    {/* <li className="mb-2 host-clock"><a href=""> <img src={BadgeClock} alt="" /> </a></li> */}
                                    {/* <li className="mb-2 host-reception"><a href="" > <span className="svgicon no-texture" dangerouslySetInnerHTML={{__html:Svg.ICON.reception}} ></span> </a></li> */}
                                </ul>
                            </div>
                            {/* <div className="bring-down" style={(!openPlayer && event_during) ? { backgroundColor: '#E75480' } : {}}>
                                <span >{openPlayer ? '^' : '^'}</span>
                            </div> */}
                        </div>
                        <div className="col-md-4 col-sm-4"></div>
                    </div>
                </div>
            </section>
        );
    }
}

const mapStateToProps = (state) => {

    return {
        main_host_data: state.NewInterface.mainHostState,

    };

};


export default connect(mapStateToProps, null)(MainHost);