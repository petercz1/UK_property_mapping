class AddIcons {

  constructor(addresses) {
    return this.add_icons(addresses);
  }

  add_icons(addresses) {
    addresses.features.forEach(this.create_icon);
    return addresses;
  }

  create_icon(address) {
    let property_color = '';
    switch (address.properties.property_type) {
      case "detached":
        property_color = "#0000ff";
        break;
      case "flat-maisonette":
        property_color = "#FF8C00";
        break;
      case "terraced":
        property_color = "#006400";
        break;
      case "semi-detached":
        property_color = "#8B0000"
        break;
      default:
        property_color = "#808080"

    }

    let icon_label = "£" + address.properties.amount / 1000 + 'k';
    let canvas = document.createElement("canvas");
    let ctx = canvas.getContext('2d');
    ctx.font = "12px Arial";
    ctx.canvas.width = ctx.measureText(icon_label).width + 6;
    ctx.fillStyle = property_color;
    ctx.fillRect(0, 0, ctx.canvas.width, 16);

    ctx.font = "12px Arial";
    ctx.fillStyle = '#fff';
    ctx.fillText(icon_label, 3, 12);

    ctx.globalAlpha = 0.5;

	address.properties.iconUrl = ctx.canvas.toDataURL();
  }
}

export default AddIcons;