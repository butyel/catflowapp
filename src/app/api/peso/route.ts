import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const body = await request.json();

    const gato = await prisma.gato.findFirst({
      where: {
        id: body.gatoId,
        ...(session.userRole !== "admin" ? { userId: session.userId } : {}),
      },
    });
    if (!gato) return NextResponse.json({ success: false, message: "Gato não encontrado" }, { status: 404 });

    const peso = await prisma.pesoHistorico.create({
      data: {
        gatoId: body.gatoId,
        peso: parseFloat(body.peso),
        data: new Date(body.data || new Date()),
      },
    });

    await prisma.gato.update({
      where: { id: body.gatoId },
      data: { peso: parseFloat(body.peso) },
    });

    return NextResponse.json({ success: true, data: peso, message: "Peso registrado!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao registrar peso" }, { status: 500 });
  }
}
