
/**
 * @type {Object} 
 * @property {String} props.SubHeading Small heading placed at top of content setting section in design setting.
 * @property {Function} props.callBack Function for color picker to take current value.
 * @property {Function} props.callBackCancel Function for cancel button in content setting section.
 * @property {Function} props.child Function which return child components for content setting setion when maiun switch is 
 * ON.
 * @property {Boolean} props.color_modal Boolean to render reset color modal in content setting section.
 * @property {String} props.dataKey Unique key for component.
 * @property {Function} props.getKeyData Function to get current value for a specific key eg - get data for content setting
 * section. 
 * @property {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @property {String} props.heading Main heading text placed at top of content setting section in design setting.
 * @property {Object} props.icon Icon to update the value
 * @property {String} props.icon.field Icon field name (ex:-business_team_icon)
 * @property {String} props.icon.value Icon updated image url (ex:-https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/label_icons/business_team_altImage/galaxy-space-mu-1366x768.jpg)
 * @property {Function} props.resetColorHandler Function to reset all colors of content setting section in primary color.
 * @property {String} props.reset_color Currrent section name to render reset color functionality into it eg - here
 * reset_color value will be "content".
 * @property {Function} props.updateDesignSetting Function used to update design setting values.
 * @property {TooltipObject} props.tooltip_labels Tooltip object.
 */

const DesignDataWithReset = {
    SubHeading: "Customise your Event's Content section",
    callBack: data => {},
    callBackCancel: () => {},
    child: props => {},
    color_modal: true,
    dataKey: "content_customized",
    getKeyData: key => {},
    graphicSetting: (53) [{}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}],
    heading: "Content",
    icon: {},
    resetColorHandler: () => {},
    reset_color: "content",
    setShowContentCropPreview: ()=>{},
    showContentCropPreview: false,
    tooltip_labels:{},
    updateDesignSetting: ()=>{},
}

export default DesignDataWithReset