import React, {useEffect, useState} from "react";
import "./GridComponent.css";
import {connect} from "react-redux";
import GridMediaBanner from "./GridMediaBanner/GridMediaBanner";
import UserGrid from "./UserGrid/UserGrid";
import EmptyGrid from "./EmptyGrid/EmptyGrid";

let GridComponent = (props) => {
    const [gridSectionSceneryColor, setGridSectionSceneryColor] = useState(null);

    useEffect(() => {
        if (props.eventScenery) {
            let {
                user_grid_background,
                user_grid_customization,
                event_color_2,
                customized_colors,
            } = props.eventGraphics;

            let SCENERY_DATA = props.eventScenery;

            if (customized_colors === 1 && user_grid_customization === 1) {
                setGridSectionSceneryColor({
                    "background": `rgba(${user_grid_background.r}, ${user_grid_background.g},${user_grid_background.b},${SCENERY_DATA.component_opacity})`,
                })
            } else if (customized_colors === 1) {
                setGridSectionSceneryColor({
                    "background": `rgba(${event_color_2.r}, ${event_color_2.g},${event_color_2.b},${SCENERY_DATA.component_opacity})`,
                })
            }
        }
    }, [props.eventGraphics, props.eventScenery]);


    if (!props.gridMeta.visible) {
        return <></>
    }

    return (
        <div className="container grid-padding">
            This is test 1
            <div className="grid-section kct-customization" style={gridSectionSceneryColor}>
                {/* Showing the media if event is not live */}
                {
                    !props.event_data?.event_is_live
                        ? <GridMediaBanner />
                        : props.allowNetworking &&
                        <>
                            <EmptyGrid />
                            <UserGrid />
                        </>
                }
            </div>
        </div>
    );
};

const mapStateToProps = (state) => {
    return {
        gridMeta: state.NewInterface.gridMeta,
        event_data: state.NewInterface.interfaceEventData,
        eventGraphics: state.Graphics.eventGraphics,
        eventScenery: state.Graphics.eventScenery,
        spaceHost: state.NewInterface.interfaceSpaceHostData,

        allowNetworking: state.Dashboard.networkingState.allowed,
        gridPagination: state.NewInterface.gridPagination,

    };
};

GridComponent = connect(mapStateToProps)(GridComponent);
export default GridComponent;



