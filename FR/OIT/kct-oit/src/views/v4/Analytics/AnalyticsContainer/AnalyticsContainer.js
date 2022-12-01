import {useEffect, useState} from "react";
import {useDispatch} from "react-redux";
import groupAction from "../../../../redux/action/apiAction/group";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This container is used for analytics component to get groups data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Array}
 */
const useAnalyticsGroupData = () => {
    const dispatch = useDispatch();
    const [groupData, setGroupData] = useState([]);
    const gData = [];
    useEffect(() => {
        try {
            dispatch(groupAction.getGroups({filter: "pilot"}))
                .then((res) => {
                const data = res.data.data;

                data.map((value) => {
                        gData.push(value);
                });
                setGroupData(gData);
            });
        } catch (err) {
            console.log(err);
        }
    }, [])
    return {
        get: groupData,
        set: setGroupData,
    };

}

export default useAnalyticsGroupData;