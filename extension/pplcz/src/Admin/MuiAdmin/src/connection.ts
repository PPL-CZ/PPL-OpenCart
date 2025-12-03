export const baseConnectionUrl = (): {
  nonce: "string";
  url: "string";
} => {
  // @ts-ignore
  return window["pplcz_data"];
};

export const makeUrl = (controller: string, QueryValues?: { [path: string]: any }) => {
  // @ts-ignore
  const template = window.pplcz_data;

  const a = document.createElement("a");
  a.href = template;
  const url = new URL(a.href);

  Object.keys(QueryValues || {}).forEach(key => {
    if (QueryValues?.[key] === undefined || QueryValues?.[key] === null) return;

    let value = `${QueryValues[key]}`;
    url.searchParams.set(key, value);
  });

  controller = `extension/pplcz/shipping/pplcz|${controller}`;
  url.searchParams.set("route", controller);

  return url;
};

export const makeOrderUrl = (orderId: string) => {
  const a = document.createElement("a");
  a.href = `${document.location}`;

  const url = new URL(a.href);
  url.searchParams.set("route", "sale/order.info");
  url.searchParams.set("order_id", orderId);
  return `${url}`;
};

export const makePrintUrl = (
  batchId: string,
  shipmentId: string | null,
  packageId?: string | null,
  print?: string | null
) => {
  const a = document.createElement("a");
  a.href = `${document.location}`;

  const url = new URL(a.href);
  url.searchParams.set("batch_id", batchId);
  if (shipmentId) {
    url.searchParams.set("shipment_id", shipmentId);
    if (packageId) url.searchParams.set("package_id", packageId);
  }
  if (print) url.searchParams.set("print", print);

  url.searchParams.set("route", "extension/pplcz/shipping/pplcz.printBatchShipment");
  return `${url}`;
};
