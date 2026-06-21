import Image from "next/image";
import logo from "../public/wordmark.svg";

export default function Home() {
    return (
        <div className="flex justify-center items-center gap-2">
            <Image src={logo} alt="logo" height={50} />
        </div>
    );
}
