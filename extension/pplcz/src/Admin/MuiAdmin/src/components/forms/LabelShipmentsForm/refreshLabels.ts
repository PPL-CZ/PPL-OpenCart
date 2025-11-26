import { UseFormSetValue } from "react-hook-form";
import { makeUrl } from "../../../connection";
import { components } from "../../../schema";

type CreateShipmentLabelBatchModel = components["schemas"]["CreateShipmentLabelBatchModel"];
type RefreshShipmentBatchReturnModel = components["schemas"]["RefreshShipmentBatchReturnModel"];
type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentWithAdditionalModel[];
};

export const refreshLabels = async (
  batchId: string,
  shipmentId: number[],
  printForm: string,
  controller: AbortController,
  setValue: UseFormSetValue<CreteLabelShipmentItems>
) => {
  const url = makeUrl("refreshBatchLabels", {
    batch_id: batchId,
  });

  try {
    while (true) {
      const response = await fetch(url, {
        headers: {
          "Content-Type": "application/json",
        },
        signal: controller.signal,
        method: "POST",
        body: JSON.stringify({ shipmentId } as CreateShipmentLabelBatchModel),
      });

      if (response.status === 200) {
        const retModel = (await response.json()) as RefreshShipmentBatchReturnModel;
        retModel.shipments?.forEach((x, index) => {
          // @ts-ignore
          setValue(`item.${index}`, x);
        });
        if (retModel.batchs?.length) {
          // @ts-ignore
          const a = document.createElement("a");
          a.href = retModel.batchs[0];

          const url = new URL(a.href);
          url.searchParams.append("print", printForm);
          return url;
        }
      }

      await new Promise<void>((res, rej) => {
        let timeout: any = null;
        let abort = (success: any) => {
          clearTimeout(timeout);
          controller.signal.removeEventListener("abort", abort);
          success === true ? res() : rej();
        };
        timeout = setTimeout(() => abort(true), 5000);
        controller.signal.addEventListener("abort", abort);
      });
    }
  } catch (e) {
    return null;
  }
};

export default refreshLabels;
